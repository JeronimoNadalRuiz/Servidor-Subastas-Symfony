<?php

namespace App\Controller;

use App\Entity\Puja;
use App\Entity\Subasta;
use App\Entity\User;
use App\Services\JwtAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PujaController extends AbstractController
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
                $pujaUser = (!empty($params->puja)) ? $params->puja : null;
                $createdAt = (!empty($params->createdAt)) ? (new \DateTime('now')) : (new \DateTime('now'));

                $subastaRepo = $this->getDoctrine()->getRepository(Subasta::class);
                $subasta = $subastaRepo->findOneBy([
                    'id' => $subastaId
                ]);


                $pujaRepo = $this->getDoctrine()->getRepository(Puja::class);
                $pujaMax = $pujaRepo->findBy([
                    'subasta' => $subastaId
                ],
                    ['puja'=>'DESC'],
                    [1]
                );

                if (!empty($userId) && !empty($subasta)) {
                    $doctrine = $this->getDoctrine();
                    $em = $doctrine->getManager();
                    $userRepo = $this->getDoctrine()->getRepository(User::class);
                    $user = $userRepo->findOneBy([
                        'id' => $identity->sub
                    ]);
                    $puja = new Puja();
                    $puja->setUser($user);
                    $puja->setSubasta($subasta);
                    $puja->setPuja($pujaUser);
                    $puja->setCreatedAt(new \DateTime('now'));

                    $em->persist($puja);
                    $em->flush();
                    $data = [
                        'status' => 'success',
                        'code' => '200',
                        'message' => 'Puja creada correctamente'
                    ];

                }else{
                    $data = [
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'Datos incorrectos o no puedes hacer una puja a una subasta tuya o no puedes hacer una puja con valor inferior al de salida o menor que la puja maxima'

                    ];
                }
            }
        }
        return new JsonResponse($data);
    }
    public function getPujasUser(Request $request, JwtAuth $jwtAuth){

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

                $subastas = $em->createQuery(
                    'SELECT s 
                     FROM App:Subasta s,
                    App\Entity\Puja p
                    where p.user=:user
                    and s.id=p.subasta
                ')->setParameter('user', $userId);

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

    public function getPujasSubasta(Request $request, JwtAuth $jwtAuth, $id){
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
                $pujaRepo = $this->getDoctrine()->getRepository(Puja::class);
                $pujaMax = $pujaRepo->findBy([
                    'subasta' => $id
                ],
                    ['puja'=>'DESC'],
                    [1]
                );
                if($pujaMax){
                    $data = [
                        'status' => 'success',
                        'code' => '200',
                        'message' => $pujaMax
                    ];
                }else{
                    $data = [
                        'status' => 'success',
                        'code' => '400',
                        'message' => 'No hay pujas para esta subasta'
                    ];
                }


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
