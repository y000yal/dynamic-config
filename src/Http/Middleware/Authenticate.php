<?php

namespace GeniussystemsNp\DynamicConfig\Http\Middleware;

use App\Models\ResellerAuthCode;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;


class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Authenticate constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */

    public function handle($request, Closure $next, $scopes = null)
    {
        try{
            if ($request->has('access_token')) {
                $request->headers->set('Authorization', 'Bearer ' . $request->get('access_token'));
            }

            if(!is_null($scopes)) {

                $scopes = explode("|", $scopes);

                $token = $request->bearerToken();
                if (!empty($token)) {
                    $validity = $this->validateToken($request, $token, $scopes);
                    if(is_string($validity)){
                        if($validity == "Unauthorized")
                        {
                            throw new \Exception($validity);

                        }else{
                            return  response()->json([
                                                             "message" => $validity,
                                                     ], 400);
                        }
                    }

                } else {
                    $appKey = $request->header('api-key');
                    $appSecret = $request->header('api-secret');

                    if (!empty($appKey) && !empty($appSecret) && in_array("client_auth", $scopes)) {

                        $validity = $this->validateApiKeySecret($request,$appKey, $appSecret);
                        if(is_string($validity)){
                            if($validity == "Unauthorized")
                            {
                                throw new \Exception($validity);

                            }else{
                                return  response()->json([
                                                                 "message" => $validity,
                                                         ], 400);
                            }
                        }

                    } else {

                        throw new \Exception('Unauthorized');
                    }
                }
            }

            return $next($request);

        } catch (\Exception $ex) {
            return response()->json([
                                            "message" => $ex->getMessage(),
                                    ], 401);
        }

    }

    /**
     * Validate Token
     * @param $request
     * @param $token
     * @param $scopes
     * @return bool|string
     */

    private function validateToken($request, $token, $scopes)
    {
        try {

            $token = (new Parser())->parse($token);


            $signer = new Sha256();
            $publicKey = 'file://' . storage_path('oauth-public.key');


            if (!$token->verify($signer, $publicKey)) {
                return "Unauthorized";
            }

            $data = new ValidationData();
            $data->setCurrentTime(time());
            if (!$token->validate($data)) {
                return "Unauthorized";

            }
            $scope = $token->getClaim('scopes');


            if (in_array($scope[0], $scopes)) {
                // dd('dd');

                $tokenParams = $token->getClaim('params');

                if ($scope[0] == "reseller") {
                    if (!is_null($tokenParams->expiry_date)) {
                        /**
                         * Check if current time is greater than the expiry date in token.
                         * If true, throw account expiry message.
                         * Else continue.
                         */

                        if (Carbon::now()->greaterThan($tokenParams->expiry_date)) {
                            return "Your account has been expired. Please contact your service provider for more info.";
                        }

                    }

                }

                if ($scope[0] == "subscriber") {
                    if (!is_null($tokenParams->expiry_date)) {
                        /**
                         * Check if current time is greater than the expiry date in token.
                         * If true, throw account expiry message.
                         * Else continue.
                         */

                        if (Carbon::now()->greaterThan($tokenParams->expiry_date) && $tokenParams->content_expiry == "1") {
                            return "Your service provider's account has been expired. Please contact your service provider for more info.";
                        }

                    }

                }
                $request->attributes->set('scope', $scope[0]);
                $request->attributes->set('guard', $scope[0]);

                if ($tokenParams) {

                    $request->attributes->set('params', $tokenParams);

                }

                return true;

            }
            return "Unauthorized";
        }catch (\Exception $ex){
            return "Unauthorized";
        }

    }

    /**
     * Validate API key and Secret.
     * @param $request
     * @param $key
     * @param $secret
     * @return \Illuminate\Http\JsonResponse|string
     */
    private function validateApiKeySecret($request,$key, $secret)
    {

        try {
            $data = ResellerAuthCode::where([
                                                    ["app_key", $key],
                                                    ["app_secret", $secret],
                                            ])->with(['reseller'])->firstOrFail()->toArray();

            if (!is_null($data['reseller']['expiry_date'])) {
                /**
                 * Check if current time is greater than the expiry date in token.
                 * If true, throw account expiry message.
                 * Else continue.
                 */

                if (Carbon::now()->greaterThan($data['reseller']['expiry_date'])) {
                    return "Your account has been expired. Please contact your service provider for more info.";
                }

            }

            $request->attributes->set('scope', 'reseller');
            $request->attributes->set('guard', 'reseller');
            $request->attributes->set('auth_type', 'client');

            $tokenParams = (object)[
                    "id" => $data['reseller']['id'],
                    "username" => $data['reseller']['username'],
                    "reseller_id" => $data['reseller']['reseller_id'],
                    "expiry_date" => $data['reseller']['expiry_date'],
                    "content_expiry" => $data['reseller']['content_expiry_flag'],
            ];


            $request->attributes->set('params', $tokenParams);


        } catch (ModelNotFoundException $ex) {
            return "Unauthorized";

        } catch (\Exception $ex){
            return "Unauthorized";

        }

    }


}
