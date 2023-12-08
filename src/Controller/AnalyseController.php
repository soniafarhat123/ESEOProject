<?php

namespace App\Controller;

use App\Entity\Analyse;
use App\Form\AnalyseEditType;
use App\Form\AnalyseType;
use App\Repository\AnalyseRepository;
use Doctrine\ORM\EntityManagerInterface;
use mysqli;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;



class AnalyseController extends AbstractController
{
    #[Route('/user/analyse', name: 'analyse.index',methods: ['GET'])]
    public function index(AnalyseRepository $repository,PaginatorInterface $paginator,Request $request): Response
    {
        $user = $this->getUser();
        $analyse = $paginator->paginate(
            $repository->findBy(
                array(),
                array('id' => 'DESC')
            ),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/analyse/index.html.twig', [
            'image' => $analyse,
            'user' =>$user
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/user/image/nouvelle', name: 'image.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $user=$this->getUser();
        $image = new Analyse();
        $form = $this->createForm(AnalyseType::class, $image);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($image->getImage() != null){
                if($image->getResultat() === "Saine"){
                    // On récupère les images transmises
                    $file = $form->get('image')->getData();
                    // On génère un nouveau nom de fichier
                    $fileName = $image->getNomImage() . '.' . $file->guessExtension();
                    // On copie le fichier dans le dossier uploads
                    $file->move($this->getParameter('image_directory_saine'), $fileName);
                    // On crée l'image dans la base de données
                    $image->setImage($fileName);
                }
                else{
                    // On récupère les images transmises
                    $file = $form->get('image')->getData();
                    // On génère un nouveau nom de fichier
                    $fileName = $image->getNomImage() . '.' . $file->guessExtension();
                    // On copie le fichier dans le dossier uploads
                    $file->move($this->getParameter('image_directory_malade'), $fileName);
                    // On crée l'image dans la base de données
                    $image->setImage($fileName);
                }
            }

            $manager->persist($image);
            $manager->flush();

            $this->AddFlash(
                'success',
                'Votre image a été ajouté avec succés !'
            );

            return $this->redirectToRoute('analyse.index');
        }
        return $this->render('pages/analyse/new.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user
            ]);
    }

    #[Route('/user/image/modification/{id}',name: 'image.edit', methods: ['GET','POST'])]
    public function edit(Analyse $image, Request $request, EntityManagerInterface $manager)
    {

        $user = $this->getUser();
        $form = $this->createForm(AnalyseEditType::class,$image);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //$image->setImage($image->getImage());
            $manager->persist($image);
            $manager->flush();

            $this->AddFlash(
                'success',
                'Votre image a été modifié avec succés !'
            );

            return $this->redirectToRoute('analyse.index');
        }
        return $this->render('pages/analyse/edit.html.twig',
            [
                'form' => $form->createView(),
                'user'=> $user,
                'image' => $image
            ]);
    }

    #[Route('/user/image/suppression/{id}',name: 'image.delete',methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Analyse $image){

        if(!$image){
            $this->AddFlash(
                'success',
                'L\'Image en question n\'a pas été trouvé !'
            );
            $this->redirectToRoute('analyse.index');
        }

        /**
         * Je gère la suppression du dossier "uploads" ou l'image est stockée
         */
        //Je récupère le nom de l'image
        $filename = $image->getImage();
        // Je crée une instance de kla classe fileSystem
        $fileSystem = new Filesystem();
        //Je supprime l'image du dossier
        if($image->getResultat() === "Saine"){
            $projectDir= $this->getParameter('kernel.project_dir');
            $fileSystem->remove($projectDir."/public/images/Saine/".$filename);
        }
        else{
            $projectDir= $this->getParameter('kernel.project_dir');
            $fileSystem->remove($projectDir."/public/images/Malade/".$filename);
        }

        $manager->remove($image);
        $manager->flush();

        $this->AddFlash(
            'success',
            'Votre Image a été supprimé avec succés !'
        );

        return $this->redirectToRoute('analyse.index');
    }

    #[Route('/user/excel',name: "image.excel",methods: ['GET'] )]
    public function export(AnalyseRepository $repository)
    {
        $db_host = 'localhost';
        $db_username = 'root';
        $db_password = '';
        $db_name = 'gcadb';

        $db = new mysqli($db_host, $db_username, $db_password, $db_name);

        if($db->connect_error){
            die("Unable to connect database: " . $db->connect_error);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add data to the spreadsheet
        $imagesName = $repository->selectNom();
        $imagesResult = $repository->selectResultat();
        $sheet->setCellValue('A1', 'Nom Image');
        $sheet->setCellValue('B1', 'Résultat');

        $query = $db->query('SELECT * FROM analyse');
        if($query->num_rows > 0) {
            $i = 2;
            while ($row = $query->fetch_assoc()) {
                $sheet->setCellValue('A' . $i, $row['nom_image']);
                $sheet->setCellValue('B' . $i, $row['resultat']);
                $i++;
            }
        }


        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);

        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->getParameter('excel_directory');
        // e.g /var/www/project/public/my_first_excel_symfony4.xlsx
        $date = new \DateTime();
        $date_str = trim($date->format('d_m_Y'));
        $excelFilepath =  $publicDirectory . '/fichier_excel_'.$date_str.'.xlsx';
        // Create the file
        $writer->save($excelFilepath);
        // Return a text response to the browser saying that the excel was succesfully created

        $this->AddFlash(
            'success',
            'Fichier excel exporté avec succés !'
        );

        return $this->redirectToRoute('analyse.index');



    }
}
