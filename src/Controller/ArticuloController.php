<?php

namespace App\Controller;

use App\Entity\Lote;
use App\Entity\Articulo;
use App\Services\JwtAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticuloController extends AbstractController
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
                $loteId = (!empty($params->loteId)) ? $params->loteId : null;
                $titulo = (!empty($params->titulo)) ? $params->titulo : null;
                $descripcion = (!empty($params->descripcion)) ? $params->descripcion : null;

                $loteRepo = $this->getDoctrine()->getRepository(Lote::class);
                $lote = $loteRepo->findOneBy([
                    'id' => $loteId
                ]);

                if (!empty($userId) && !empty($titulo) && !empty($lote) && $lote->getSubasta()->getUser()->getId()==$identity->sub) {
                    $doctrine = $this->getDoctrine();
                    $em = $doctrine->getManager();

                    $articulo = new Articulo();
                    $articulo->setLote($lote);
                    $articulo->setTitulo($titulo);
                    $articulo->setDescripcion($descripcion);

                    $em->persist($articulo);
                    $em->flush();
                    $data = [
                        'status' => 'success',
                        'code' => '200',
                        'message' => 'Articulo creado correctamente'
                    ];

                }else{
                    $data = [
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'Datos incorrectos o no puedes crear un articulo de un lote que no es tuyo'
                    ];
                }
            }
        }
        return new JsonResponse($data);

    }
}
