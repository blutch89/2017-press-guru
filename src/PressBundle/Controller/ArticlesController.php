<?php

namespace PressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use PressBundle\Services\SiteExtractor;
use PressBundle\Services\SiteExtractorInterface;
use PressBundle\Entity\Article;

class ArticlesController extends Controller {
    
    public function getAllAction() {
        $em = $this->getDoctrine()->getManager();
        $articlesRepository = $em->getRepository("PressBundle:Article");
        $user = $this->get('security.context')->getToken()->getUser();
        
        $articles = $articlesRepository->getArticlesFromUser($user->getId());

        return new JsonResponse([
            "success" => true,
            "articles" => $articles
        ], 200);
    }
    
    public function addAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $siteExtractor = $this->get("press.site_extractor");
        $user = $this->get('security.context')->getToken()->getUser();
        $validator = $this->get('validator');
        
        // Paramètres de requête
        $url = $request->request->get("url");
        
        // Tests
        if ($url == null) {
            return $this->sendErrorMessage("Veuillez entrer une url.");
        }
        
        try {
            // Extraction des informations du site
            $infos = $siteExtractor->extractAllDatas($url);
            
            // Création de l'article
            $article = new Article();
            $article->setTitle($infos["title"]);
            $article->setDescription($infos["description"]);
            $article->setPicture($infos["image"]);
            $article->setLink($url);
            $article->setArchived(false);
            $article->setOwner($user);
            
            // Validations
            $validationErrors = $validator->validate($article);
            
            if (count($validationErrors) > 0) {
                return $this->sendErrorMessage($validationErrors[0]->getMessage());
            }
            
            // Sauvegarde
            $em->persist($article);
            $em->flush();
            
            return new JsonResponse(["success" => true], 200);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "failed to open stream") !== false) {
                return $this->sendErrorMessage("L'url spécifiée est introuvable.");
            }
            
            return $this->sendErrorMessage("Une erreur inconnue s'est produite.//".$e->getMessage());
        }
    }
    
    private function sendErrorMessage($errorMessage) {
        return new JsonResponse([
            'success' => false,
            'error' => $errorMessage
        ], 500);
    }
}
