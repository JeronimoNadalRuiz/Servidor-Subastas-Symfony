<?php

namespace App\Controller;

use App\Entity\Subasta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Services\JwtAuth;

class SubastaController extends AbstractController
{
    public function create(Request $request, JwtAuth $jwtAuth){

        $token = $request->headers->get('Authorization');

        $checkToken=$jwtAuth->checkToken($token);

        $data = [
            'status' => 'error',
            'code' => '400',
            'message' => 'Usuario no Autorizado por token',
            'token'=>$token,
            'checkToken'=>$checkToken
        ];

        if($checkToken) {
            $json = $request->get('json',null);

            if (!empty($json)) {
                $data =$json;

                $params = json_decode($json);
                $identity = $jwtAuth->checkToken($token, true);

                $userId = ($identity->sub!=null) ? $identity->sub : null;
                $titulo = (!empty($params->titulo)) ? $params->titulo : null;
                $precio = (!empty($params->precio)) ? $params->precio : null;
                $descripcion = (!empty($params->descripcion)) ? $params->descripcion : null;
                $fechaInicio = (!empty($params->fechaInicio)) ? (new \DateTime($params->fechaInicio)) : (new \DateTime('now'));
                $fechaFin = (!empty($params->fechaFin)) ? (new \DateTime($params->fechaFin)) : (new \DateTime('now'));


                if (!empty($userId)&& !empty($titulo)&& !empty($precio)) {
                    $doctrine = $this->getDoctrine();
                    $em = $doctrine->getManager();
                    $userRepo = $this->getDoctrine()->getRepository(User::class);
                    $user = $userRepo->findOneBy([
                        'id' => $identity->sub
                    ]);
                    $subasta = new Subasta();
                    $subasta->setUser($user);
                    $subasta->setTitulo($titulo);
                    $subasta->setPrecio($precio);
                    $subasta->setDescripcion($descripcion);
                    $subasta->setFechaInicio($fechaInicio);
                    $subasta->setFechaFin($fechaFin);

                    $em->persist($subasta);
                    $em->flush();
                    $data = [
                        'status' => 'success',
                        'code' => '200',
                        'message' => $subasta
                    ];

                }
            }
        }
        return new JsonResponse($data);

    }

    public function getSubastas(Request $request, JwtAuth $jwtAuth){

        $token = $request->headers->get('Authorization');

        $checkToken=$jwtAuth->checkToken($token);

        $data = [
            'status' => 'error',
            'code' => '400',
            'message' => 'Usuario no Autorizado por token',
            'token'=>$token,
            'checkToken'=>$checkToken
        ];

        if($checkToken) {
            $identity = $jwtAuth->checkToken($token, true);

            $userId = ($identity->sub!=null) ? $identity->sub : null;

            if (!empty($userId)) {
                $em = $this->getDoctrine()->getManager();
                $subastaRepo = $this->getDoctrine()->getRepository(Subasta::class);
                $fechaActual=date('Y-m-d H:i:s');
                $sql=" {$fechaActual}";
                $subastas = $em->createQuery(
                    'SELECT s FROM APP\Entity\Subasta s WHERE s.fechaFin >=:fecha')
                    ->setParameter('fecha',$fechaActual);
                $subastas = $subastas->getResult();

                $data = [
                    'status' => 'success',
                    'code' => '200',
                    'message' => $subastas
                ];

            }else {
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Datos incorrectos'

                ];
            }

        }
        return new JsonResponse($data);
    }

    public function getSubastasUser(Request $request, JwtAuth $jwtAuth){

        $token = $request->headers->get('Authorization');

        $checkToken=$jwtAuth->checkToken($token);

        $data = [
            'status' => 'error',
            'code' => '400',
            'message' => 'Usuario no Autorizado por token',
            'token'=>$token,
            'checkToken'=>$checkToken
        ];

        if($checkToken) {
            $identity = $jwtAuth->checkToken($token, true);

            $userId = ($identity->sub!=null) ? $identity->sub : null;

            if (!empty($userId)) {
                $em = $this->getDoctrine()->getManager();
                $subastaRepo = $this->getDoctrine()->getRepository(Subasta::class);
                $fechaActual=date('Y-m-d H:i:s');
                $subastas = $subastaRepo->findBy([
                    'user' => $userId
                ]);

                $data = [
                    'status' => 'success',
                    'code' => '200',
                    'message' => $subastas
                ];

            }else {
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Datos incorrectos'

                ];
            }

        }
        return new JsonResponse($data);
    }
}
