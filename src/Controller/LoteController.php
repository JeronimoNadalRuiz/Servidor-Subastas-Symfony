<?php

namespace App\Controller;

use App\Entity\Subasta;
use App\Entity\Lote;
use App\Services\JwtAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LoteController extends AbstractController
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
                $subastaId = (!empty($params->subastaId)) ? $params->subastaId : null;
                $titulo = (!empty($params->titulo)) ? $params->titulo : null;
                $descripcion = (!empty($params->descripcion)) ? $params->descripcion : null;

                $subastaRepo = $this->getDoctrine()->getRepository(Subasta::class);
                $subasta = $subastaRepo->findOneBy([
                    'id' => $subastaId
                ]);

                if (!empty($userId) && !empty($titulo) && !empty($subasta) && $subasta->getUser()->getId()==$identity->sub) {
                    $doctrine = $this->getDoctrine();
                    $em = $doctrine->getManager();

                    $lote = new Lote();
                    $lote->setSubasta($subasta);
                    $lote->setTitulo($titulo);
                    $lote->setDescripcion($descripcion);

                    $em->persist($lote);
                    $em->flush();
                    $data = [
                        'status' => 'success',
                        'code' => '200',
                        'message' => $lote
                    ];

                }else{
                    $data = [
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'Datos incorrectos o no puedes crear un lote de una subasta que no es tuya'
                    ];
                }
            }
        }
        return new JsonResponse($data);

    }

    public function getLotesSubasta(Request $request, JwtAuth $jwtAuth, $id){

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
                $loteRepo= $this->getDoctrine()->getRepository(Lote::class);

                $lotes = $loteRepo->findBy([
                    'subasta' => $id
                ]);

                $data = [
                    'status' => 'success',
                    'code' => '200',
                    'message' => $lotes
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
