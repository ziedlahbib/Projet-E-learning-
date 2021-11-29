<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Cours;
use App\Form\CourssearchType;
use App\Form\CoursType;
use App\Repository\CategorieRepository;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Knp\Component\Pager\PaginatorInterface;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
class CoursController extends AbstractController
{
    /**
     * @Route("/cours", name="cours")
     */
    public function index(): Response
    {
        return $this->render('cours/index.html.twig', [
            'controller_name' => 'CoursController',
        ]);
    }
    /**
     * @Route("/AfficheCours",name="AfficheCours")
     */
    public function Affiche(CoursRepository $repository){
//$repo=$this->getDoctrine()->getRepository(Classroom::class);
        $tableCours=$repository->findAll();
        return $this->render('cours/tablecours.html.twig'
            ,['tablecours'=>$tableCours]);

    }

    /**
     * @Route("/supprimercours/{id}",name="supprimercours")
     */
    public function delete($id,EntityManagerInterface $em ,CoursRepository $repository){
        $cours=$repository->find($id);
        $em->remove($cours);
        $em->flush();
        $this->addFlash(
            'info','Supprimé avec succées '
        );
       // $flashy->success('Event created!', 'http://your-awesome-link.com');
        return $this->redirectToRoute('AfficheCours');
    }

    /**
     * @Route("/ajoutcours",name="ajoutcours")
     */
    public function addCours(EntityManagerInterface $em,Request $request,\Swift_Mailer $mailer  /*,FlashyNotifier $flashy*/ ){
        $cours= new Cours();
        $form= $this->createForm(CoursType::class,$cours);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);
     //  $id = new Categorie();

        if($form->isSubmitted() && $form->isValid()){
            $new=$form->getData();
            $imageFile = $form->get('coursimg')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        'front\web\images',
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $cours->setCoursimg($newFilename);
            }
            $em->persist($cours);
            $em->flush();
            $this->addFlash(
                'info','Added successfully!');

  ////////Hedhi mail/////
            $message = (new \Swift_Message('Vous avez ajouté un nouveau cours'))
                ///expediteur
                ->setFrom('fares.moalla1996@gmail.com')
                ///destinataire
                ->setTo('9ariniphoenix@gmail.com')
                //message avec vue twig
                ->setBody(
                    $this->renderView(
                        'email/contact.html.twig',compact('new')
                    ),
                    'text/html'
                ) ;
            $mailer->send($message);
           //  $flashy->success('Event created!', 'http://your-awesome-link.com');

            return $this->redirectToRoute("AfficheCours");
        }
        return $this->render("cours/ajoutCours.html.twig",array("form"=>$form->createView()));
    }



    /**
     * @Route("/{id}/edit", name="modifiercours", methods={"GET","POST"})
     */
    public function edit(Request $request, Cours $cours): Response
    {
        $form = $this->createForm(CoursType::class, $cours);
        $form->add('Confirmer',SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('coursimg')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        'front\web\images',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $cours->setCoursimg($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'info',
                'Vos modifications ont été enregistrées!'
            );

            return $this->redirectToRoute('AfficheCours');
        }

        return $this->render('cours/Modifiercours.html.twig', [
            'coursmodif' => $cours,
            'form' => $form->createView(),
        ]);
    }


/////////////////
    /**
     * @Route("/StatCours", name="StatCours")
     */
    public function statAction(CategorieRepository $coursRepository)
    {
$categoriee= $coursRepository->findAll();
$categorie= [];
$coursCount= [];
foreach($categoriee as $categorie ){
    $categorienom[]=$categorie->getCategorienom();
    $categorieCount[]= count($categorie->getCours());
}

        return $this->render('cours/stat.html.twig',
            [
                'categorieNom' => json_encode($categorienom),
                'categorieCount' => json_encode($categorieCount)
            ]);


    }
    /// ///////////

























    /**
     * @Route("/ClientIndex", name="Client_index", methods={"GET"})
     */
    public function ClientIndex(CoursRepository $repository): Response
    {
        $cours = $repository->findAll();

     /*   return $this->render('cours/show.html.twig', [
            'cours' => $cours,
        ]);*/
    }






















}
