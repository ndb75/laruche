<?php

namespace App\Service;

use App\Entity\Gift;
use App\Entity\Receiver;
use App\Entity\Stock;
use App\Repository\ReceiverRepository;
use App\Repository\GiftRepository;
use Doctrine\ORM\EntityManagerInterface;

class StockService extends AbstractService implements \Countable
{
    CONST COLUMN_INDEX = [
        'gift_uuid' => 0,
        'description' => 2,
        'price' => 3,
        'receiver_uuid' => 4,
        'receiver_firstname' => 5,
        'receiver_lastname' => 6,
        'country' => 7
    ];

    CONST BATCH_SIZE = 100;
    CONST UUID_LENGTH = 36;
    CONST NB_EXCEL_COLUMN = 6;

    private $targetDirectory;
    private $entityManager;
    private $receiverRepository;
    private $giftRepository;

    private $arrGift = [];
    private $arrCountry = [];
    private $arrReceiverById = [];
    private $averagePrice = 0;
    private $maxPrice = NULL;
    private $minPrice = NULL;

    public function __construct(
        $targetDirectory,
        EntityManagerInterface $entityManager,
        ReceiverRepository $receiverRepository,
        GiftRepository $giftRepository
    )
    {
        $this->targetDirectory      = $targetDirectory;
        $this->entityManager        = $entityManager;
        $this->receiverRepository   = $receiverRepository;
        $this->giftRepository       = $giftRepository;
    }

    public function count()
    {
        //return count
        return count($this->arrGift);
    }

    public function parseUploadedFile(string $fileName): void
    {
        $row = 0;
        if (($handle = fopen($this->targetDirectory."/".$fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                //do not treat the first line
                if ($row < 1 ) {
                    $row++;
                    continue;
                }
                for ($i = 0; $i < count($data); $i++) { // Loop over the data using $i as index pointer
                    //if first column is not an id, we do not
                    //check if the line contains the number of column need
                    //check if the first column is an id of 37 characters

                    if (strlen($data[self::COLUMN_INDEX['gift_uuid']]) != self::UUID_LENGTH || count($data) < self::NB_EXCEL_COLUMN) {
                        continue;
                    }
                    $html = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $data[$i]);
                    $this->arrGift[$row][] = strip_tags(preg_replace('/[^A-Za-z0-9\-\-.]/', '',$html));
                }
                $row++;
            }
            fclose($handle);
        }
        $this->treatFileInfo();
    }

    private function treatFileInfo()
    {
        $totalPrice = 0;
        $count      = 0;

        foreach ($this->arrGift as $key => $gift)
        {
            //treat price
            if (!empty($gift[self::COLUMN_INDEX['price']]) && is_numeric($gift[self::COLUMN_INDEX['price']])) {
                $totalPrice += $gift[self::COLUMN_INDEX['price']];

                if ($gift[self::COLUMN_INDEX['price']] > $this->maxPrice || is_null($this->maxPrice)) {
                    $this->maxPrice = $gift[self::COLUMN_INDEX['price']];
                }

                if ($gift[self::COLUMN_INDEX['price']] < $this->minPrice || is_null($this->minPrice)) {
                    $this->minPrice = $gift[self::COLUMN_INDEX['price']];
                }

                $count++;
            }

            //treat country
            if (!empty($gift[self::COLUMN_INDEX['country']])
                && is_string($gift[self::COLUMN_INDEX['country']])
                && strlen($gift[self::COLUMN_INDEX['country']]) == 2
                && !in_array($gift[self::COLUMN_INDEX['country']], $this->arrCountry)
            ) {
                $this->arrCountry[] = $gift[self::COLUMN_INDEX['country']];
            }
        }
        $this->averagePrice = ($count > 0) ? round($totalPrice/$count) : null;
    }

    public function insertIntoDatabase(Stock $stock)
    {
        $this->insertReceiver();
        $stock = $this->insertGift($stock);
        return $stock;
    }

    private function insertReceiver() : void
    {
        foreach (array_chunk($this->arrGift, self::BATCH_SIZE) as $giftChunked)
        {
            $this->entityManager->beginTransaction();
            foreach ($giftChunked as $gift) {
                if (!empty($gift[self::COLUMN_INDEX['receiver_firstname']])
                    && !empty($gift[self::COLUMN_INDEX['receiver_lastname']])
                    && !empty($gift[self::COLUMN_INDEX['receiver_uuid']]))
                {
                    $this->receiverRepository->insertOrUpdate(
                        $gift[self::COLUMN_INDEX['receiver_firstname']],
                        $gift[self::COLUMN_INDEX['receiver_lastname']],
                        $gift[self::COLUMN_INDEX['receiver_uuid']]
                    );
                }
            }
            $this->entityManager->commit();
        }
    }

    private function insertGift(Stock $stock) : Stock
    {
        foreach (array_chunk($this->arrGift, self::BATCH_SIZE) as $giftChunked)
        {
            foreach ($giftChunked as $giftLine) {
                //if no receiver => continue
                if (empty($giftLine[self::COLUMN_INDEX['receiver_uuid']])) {
                    continue;
                }

                $receiver   = $this->receiverRepository->findOneByUuid($giftLine[self::COLUMN_INDEX['receiver_uuid']]);
                $gift       = $this->giftRepository->findOneByUuid($giftLine[self::COLUMN_INDEX['gift_uuid']]);

                if (empty($gift)) {
                    $gift = new Gift();
                    $gift->setReceiver($receiver);
                    $gift->setStock($stock);
                    $gift->setPrice($giftLine[self::COLUMN_INDEX['price']]);
                    $gift->setDescription($giftLine[self::COLUMN_INDEX['description']]);
                    $gift->setCode('code');
                    $gift->setUuid($giftLine[self::COLUMN_INDEX['gift_uuid']]);
                    $this->entityManager->persist($gift);
                }
            }
            $this->entityManager->flush();;
        }
        return $stock;
    }

    public function getAveragePrice()
    {
        return $this->averagePrice;
    }

    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    public function getMinPrice()
    {
        return $this->minPrice;
    }

    public function getNumberOfCountries()
    {
        return count($this->arrCountry);
    }
}