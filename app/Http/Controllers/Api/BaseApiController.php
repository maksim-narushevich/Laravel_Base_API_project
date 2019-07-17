<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Services\Logging\LoggerInterface;
use App\Services\Logging\LoggerService;
use App\User;
use App\Utils\ErrorFormatter;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{

    /**
     * @var LoggerService
     */
    private $loggerService;

    public function __construct(LoggerService $loggerService){
        $this->loggerService = $loggerService;
    }

    /**
     * Get Logger Instance
     */
    protected function getLogger():LoggerInterface{
        return $this->loggerService->getService();
    }


    // EXAMPLE OF SENDING LOG
    // (SEPARATE MICROSERVICE INSTANCE)
    // #######################################
    public function testLogging(Request $request){
        $this->getLogger()->sendLog(['code'=>501,'message'=>'Internal server error (BASE API REMOTE)','host'=>$request->getHost(),'ip'=>$request->getClientIp()]);
    dd("Log successfully sent!");
    }

    /**
     * @param mixed $data
     * @param null $statusCode
     * @param null $placeholders
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function view( $data, $statusCode = null, $placeholders = null, array $headers = [])
    {
        if (!is_array($data) || !isset($data['data'])) {
            $data = array(
                "data" => !is_null($data) ? $data : array(),
            );
        }
        return response()->json($data, $statusCode);
    }


    /**
     * @param $data
     * @param null $errorCode
     * @param null $placeholders
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorView($data, $errorCode = null, $placeholders = null, array $headers = [])
    {
        return ErrorFormatter::getErrorFormat($data,$errorCode);
    }

    /**
     * @param Request $request
     * @param string $type
     * @param array $arrParams
     * @return bool
     */
    protected function getSortedCollectionData(Request $request, string $type, array $arrParams=[])
    {
        $orderBy = $request->get("order_by") ?? "id";
        $sortBy = (in_array(strtoupper($request->get("sort_by")), ["ASC", "DESC"])) ? $request->get("sort_by") : "ASC";
        if (in_array($type, ['product','review','user'])) {
            if($type==='review'){
                $collection =Review::where('product_id',$arrParams['product_id'])->orderBy($orderBy, $sortBy);
            }elseif($type==='product'){
                $collection = Product::where("id", "!=", 0)->orderBy($orderBy, $sortBy);
            }else{
                $collection = User::where("id", "!=", 0)->orderBy($orderBy, $sortBy);
            }
            return $collection->paginate($request->get("limit") ?? config('paginator.max_per_page'));
        } else {
            return false;
        }

    }

}
