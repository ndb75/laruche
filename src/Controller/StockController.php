<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Input\StockInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Parameter;
use Nelmio\ApiDocBundle\Annotation\Security;
use App\Output\Stock\StockOutput;
use App\Output\ErrorOutput;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use App\Service\FileUploadService;
use App\Service\StockService;


/**
 * @Route("/stock")
 */
class StockController extends  AbstractController
{
    /**
     * Allows you to upload your stock file
     *
     * @Route("", name="api.stock.post", methods={"POST"})
     * @OA\RequestBody(
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property="file",
     *                 type="array",
     *                 @OA\Items(type="string", format="binary")
     *             )
     *         )
     *     )
     * ),
     * @OA\Response(
     *     response=Response::HTTP_CREATED ,
     *     description="File is uploaded",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=StockOutput::class))
     *     )
     * )
     * @OA\Response(
     *     response=Response::HTTP_UNPROCESSABLE_ENTITY,
     *     description="Missing parameter or for forbidden values",
     *     @OA\Schema(
     *         type="object",
     *         ref=@Model(type=ErrorOutput::class)
     *     )
     * )
     * @OA\Tag(name="Stock")
     * @Security(name="Bearer")
     */
    public function uploadStockFile(
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploadService $fileUploadService,
        StockService $stockService
    ) {

        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $stock = new Stock();
        $form = $this->createForm(StockInput::class, $stock);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            //call file service to upload file into the good folder
            $filenameUploaded = $fileUploadService->uploadFile($uploadedFile);
            if ($filenameUploaded == '') {
                return $this->json(
                    ['Invalid data' => 'Invalid file format'],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            //call stock service to treat
            $stockService->parseUploadedFile($filenameUploaded);

            $stock->setFilename($filenameUploaded);
            $stock->setAveragePrice($stockService->getAveragePrice());
            $stock->setMaxPrice($stockService->getMaxPrice());
            $stock->setMinPrice($stockService->getMinPrice());
            $stock->setNbCountry($stockService->getNumberOfCountries());

            $entityManager->persist($stock);
            $entityManager->flush();

            //insert gift here
            $stock = $stockService->insertIntoDatabase($stock);
            $entityManager->clear();
        }

        return $this->json($stock, Response::HTTP_CREATED, [], ['groups' => 'api.stock.post']);
    }

    /**
     * Allows you to get statistics of your stock
     * @Route("/{stockId}", name="api.stock.get", methods={"GET"})
     * @ParamConverter("stock", options={"mapping": {"stockId" : "id"}})
     * @OA\Response(
     *     response=Response::HTTP_OK,
     *     description="Get Stock Info",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=StockOutput::class))
     *     )
     * )
     * @OA\Response(
     *     response=Response::HTTP_UNPROCESSABLE_ENTITY,
     *     description="Missing parameter or for forbidden values",
     *     @OA\Schema(
     *         type="object",
     *         ref=@Model(type=ErrorOutput::class)
     *     )
     * )
     * @OA\Tag(name="Stock")
     * @Security(name="Bearer")
     */
    public function getStockStatistics(Request $request, Stock $stock) {
        return $this->json($stock, Response::HTTP_OK,  [], ['groups' => 'api.stock.get']);
    }
}