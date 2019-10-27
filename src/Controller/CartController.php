<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     * @param SessionInterface $session
     * @param ProductRepository $productRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier=$session->get('panier',[]);
        $panierWithDAta=[];
        foreach ($panier as $id=>$quantity){

            $panierWithDAta[]=[
              'product'=>$productRepository->find($id),
                'quantity'=>$quantity
            ];
        }
        $total=0;
        foreach ($panierWithDAta as $item){
            $totalitem=$item['product']->getPrice() * $item['quantity'];
            $total +=$totalitem;
        }

        return $this->render('cart/index.html.twig', [
            'items'=>$panierWithDAta,
            'total'=>$total

        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     * @param $id
     * @param SessionInterface $session
     */
    public function add($id, SessionInterface $session){

        $panier=$session->get('panier',[]);
        if (!empty($panier[$id])){
            $panier[$id]++;
        }
        else{
            $panier[$id]=1;
        }
        $session->set('panier',$panier);
        return $this->redirectToRoute('cart_index');
    }


    /**
     * @Route("/panier/remove/{id}", name="remove_carte")
     * @param $id
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function remove ($id, SessionInterface $session){

        $panier=$session->get('panier',[]);
        if (!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('cart_index');
    }
}
