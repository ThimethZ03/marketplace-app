<?php

namespace App\Controller\User;

use App\Entity\Advert;
use App\Form\AdvertFormType;
use App\Repository\AdvertRepository;
use App\Service\CloudinaryUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/adverts')]
#[IsGranted('ROLE_USER')]
class UserAdvertController extends AbstractController
{
    #[Route('/', name: 'app_user_adverts')]
    public function index(AdvertRepository $advertRepository): Response
    {
        $adverts = $advertRepository->findByUser($this->getUser());

        return $this->render('user/advert/my_adverts.html.twig', [
            'adverts' => $adverts,
        ]);
    }

    #[Route('/create', name: 'app_user_advert_create')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        CloudinaryUploader $cloudinaryUploader
    ): Response {
        $advert = new Advert();
        $form = $this->createForm(AdvertFormType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                // Upload to Cloudinary
                $publicId = $cloudinaryUploader->upload($imageFile);
                $advert->setImage($publicId);
            }

            $advert->setUser($this->getUser());
            $entityManager->persist($advert);
            $entityManager->flush();

            $this->addFlash('success', 'Your advert has been created successfully!');

            return $this->redirectToRoute('app_user_adverts');
        }

        return $this->render('user/advert/create.html.twig', [
            'advertForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_advert_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ADVERT_EDIT', 'advert')]
    public function edit(
        Advert $advert,
        Request $request,
        EntityManagerInterface $entityManager,
        CloudinaryUploader $cloudinaryUploader
    ): Response {
        $form = $this->createForm(AdvertFormType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                // Delete old image from Cloudinary if exists
                if ($advert->getImage()) {
                    $cloudinaryUploader->delete($advert->getImage());
                }

                // Upload new image to Cloudinary
                $publicId = $cloudinaryUploader->upload($imageFile);
                $advert->setImage($publicId);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Your advert has been updated successfully!');

            return $this->redirectToRoute('app_user_adverts');
        }

        return $this->render('user/advert/edit.html.twig', [
            'advertForm' => $form->createView(),
            'advert' => $advert,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_advert_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ADVERT_DELETE', 'advert')]
    public function delete(
        Advert $advert,
        Request $request,
        EntityManagerInterface $entityManager,
        CloudinaryUploader $cloudinaryUploader
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            // Delete image from Cloudinary if exists
            if ($advert->getImage()) {
                $cloudinaryUploader->delete($advert->getImage());
            }

            $entityManager->remove($advert);
            $entityManager->flush();

            $this->addFlash('success', 'Your advert has been deleted successfully!');
        }

        return $this->redirectToRoute('app_user_adverts');
    }
}
