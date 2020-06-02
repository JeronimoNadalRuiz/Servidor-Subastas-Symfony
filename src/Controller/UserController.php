<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use App\Services\JwtAuth;

class UserController extends AbstractController
{
    /*private function resjson($data){
        $json = $this->get('serializer')->serialize($data, 'json');
        $response = new Response();
        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }*/

    /*public function index()
    {
        return $this->resjson('hola');
    }*/

    public function create(Request $request){
        $json= $request->get('json',null);
        $params = json_decode($json);

        $data =[
          'status'=>'error',
          'code'=>'200',
          'message'=>'El usuario no se ha creado',
        ];

        if($json!=null){
            $nombre = (!empty($params->nombre)) ? $params->nombre : null;
            $email = (!empty($params->email)) ? $params->email : null;
            $password = (!empty($params->password)) ? $params->password : null;

            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email,[
                new Email()
            ]);

            if(!empty($email) && count($validate_email)==0 && !empty($password) &&!empty($nombre) && !empty($password)){
               $user = new User();
               $user->setNombre($nombre);
               $user->setEmail($email);
               $user->setRole("ROLE_USER");
               $user->setCreatedAt(new \DateTime('now'));

               $pwd=hash('sha256',$password);
               $user->setPassword($pwd);

               $doctrine = $this->getDoctrine();
               $em = $doctrine->getManager();
               $user_repo= $doctrine->getRepository(User::class);
               $isset_user = $user_repo->findBy(array(
                   'email'=>$email
               ));

               if(count($isset_user)==0){
                   $em->persist($user);
                   $em->flush();

                   $data =[
                       'status'=>'sucess',
                       'code'=>'200',
                       'message'=>'El usuario creado correctamente',
                       'user'=> $user
                   ];
               }else{
                   $data =[
                       'status'=>'error',
                       'code'=>'400',
                       'message'=>'El usuario ya existe',
                   ];
               }
            }
        }

        return new JsonResponse($data);
    }

    public function login(Request $request, JwtAuth $jwtAuth){

        $json = $request->get('json',null);
        $data =[
            'status'=>'error',
            'code'=>'200',
            'message'=>'El usuario no se ha podido identificar',
            'data'=>$json
        ];

        $params = json_decode($json);

        if($json !=null){
            $email = (!empty($params->email)) ? $params->email : null;
            $password = (!empty($params->password)) ? $params->password : null;
            $getToken = (!empty($params->getToken)) ? $params->getToken : null;
            $validator = Validation::createValidator();
            $validate_email=$validator->validate($email,[
                new Email()
            ]);
            if(!empty($email) && !empty($password) && count($validate_email)==0){
                $pwd = hash('sha256', $password);
                if($getToken){
                    $singup=$jwtAuth->singup($email, $pwd,$getToken);
                }else{
                    $singup=$jwtAuth->singup($email, $pwd);

                }
                return new JsonResponse($singup);
            }else{
                return new JsonResponse($data);
            }
        }
        return new JsonResponse($data);
    }

    /*public function edit(Request $request, JwtAuth $jwtAuth){

        $token = $request->headers->get('Authorization');

        $checkToken=$jwtAuth->checkToken($token);

        $data = [
            'status' => 'error',
            'code' => '400',
            'message' => 'Usuario no Acutalizado',
            'token'=>$token,
            'getToken'=>$checkToken
        ];

        if($checkToken) {
            $em = $this->getDoctrine()->getManager();
            $identity = $jwtAuth->checkToken($token, true);
            $userRepo = $this->getDoctrine()->getRepository(User::class);
            $user = $userRepo->findOneBy([
                'id' => $identity->sub
            ]);
            $json = $request->get('json',null);
            $params = json_decode($json);
            if (!empty($json)) {
                $data =$json;
                $nombre = (!empty($params->nombre)) ? $params->nombre : null;
                $email = (!empty($params->email)) ? $params->email : null;

                $validator = Validation::createValidator();
                $validate_email = $validator->validate($email, [
                    new Email()
                ]);

                if (!empty($email) && count($validate_email) == 0  && !empty($nombre)) {
                    $user->setEmail($email);
                    $user->setNombre($nombre);

                    $isset_user=$userRepo->findBy([
                       'email' => $email
                    ]);
                    if(count($isset_user)==0 || $identity->email==$email){
                        $em->persist($user);
                        $em->flush();
                        $data = [
                            'status' => 'success',
                            'code' => '200',
                            'message' => 'Usuario actualizado correctamente'
                        ];
                    }else{
                        $data = [
                            'status' => 'error',
                            'code' => '400',
                            'message' => 'Usuario duplicado'
                        ];
                    }
                }
            }
        }
        return new JsonResponse($data);
    }*/
}
