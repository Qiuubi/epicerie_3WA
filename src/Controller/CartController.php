<?php

namespace App\Controller;

use DateTime;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    // Afficher le contenu du panier 
    #[Route('/cart', name: 'cart')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {

        // Afficher le contenu du panier 
        $cart = $session->get('cart', []);
        $cartFull = [];
        foreach ($cart as $id => $quantity) {
            $cartFull[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        /* 
        foreach ($session->get('cart', []) as $id => $quantity) {
            if($quanity > 0) {
                
            }
        }        
        
        */

        // Afficher le prix total, quantité comprise
        $total = 0;
        foreach ($cartFull as $item) {
            $totalItems = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItems;
        }

        return $this->render('cart/index.html.twig', [
            'cartContent' => $cartFull,
            'total' => $total,
        ]);
    }

    // Ajouter des produits dans le panier
    #[Route('/addCart', name: 'cart_add', methods: ['GET', 'POST'])]
    public function addCart(SessionInterface $sessionInterface, Request $request): Response
    {
        // Initialiser le panier dans la session
        $cart = $sessionInterface->get('cart', []);

        // tableau associatif clé => produit et valeur => quantité 
        $id = $request->query->get('product');
        $quantity = $request->query->get('quantity');

        // Si le panier est vide, on le remplit à chaque clic
        if (intval($quantity) && $quantity > 0) {
            if (isset($cart[$id])) {
                $cart[$id] += $quantity;
            } else {
                $cart[$id] = $quantity;
            }
        }

        // Conserver les données dans le panier 
        $sessionInterface->set('cart', $cart);

        $this->addFlash("notice", "Vous avez ajouté un produit à votre panier");


        return $this->redirectToRoute('home');
    }


    // Supprimer les produits du panier 
    #[Route('/removeCart/{id}', name: 'remove_cart', methods: ['GET', 'POST'])]
    public function removeCart(SessionInterface $sessionInterface, int $id): Response
    {
        $cart = $sessionInterface->get('cart', []);
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        $sessionInterface->set('cart', $cart);
        return $this->redirectToRoute('cart');
    }


    #[Route('/checkout', name: 'checkout')]
    public function checkout(SessionInterface $sessionInterface, UserInterface $user, ProductRepository $productRepository, EntityManagerInterface $manager): Response
    {

        // Comment ajouter une ligne de commande
        $cart = $sessionInterface->get('cart', []);
        $order = new Order;

        $total = 0;
        $deliveryPrice = 6;
        $order->setCreatedAt(new \DateTimeImmutable())
            ->setDeliveryPrice($deliveryPrice)
            ->setUser($user);

        $manager->persist($order);

        foreach ($cart as $key => $quantity) {
            $order_line = new OrderLine;
            $product = $productRepository->find($key);

            $total += $product->getPrice() * $quantity;

            $final_total = $total + $deliveryPrice;

            $order_line->setOrd($order)
                ->setQuantity($quantity)
                ->setPrice($product->getPrice() * $quantity);

            $manager->persist($order_line);
        }

        $order->setTotalPrice($final_total)
            ->setTotalQuantity($quantity);

        $manager->persist($order);
        $manager->flush();

        $cart = [];

        return $this->render('cart/checkout.html.twig', [
            'cartContent' => $cart,
        ]);
    }
}
