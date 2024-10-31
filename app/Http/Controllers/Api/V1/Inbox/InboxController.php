<?php

namespace App\Http\Controllers\Api\V1\Inbox;


use App\Http\Controllers\Controller;
use App\Library\Files;


use App\Library\MediaHelpers;
use App\Models\Conversation;
use App\Models\Inbox;
use App\Models\Item;
use App\Models\Order;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InboxController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function getlist()
    {


        $conversation=Conversation::with(array('inbox'=>function($q){
            return $q->orderBy('created_at','desc');
        }))->with(['ref_id'=>function($q){

        }])
            ->where('author_id', Auth::guard('api')->user()->id)
            ->orderBy('updated_at','desc')->get();

        return response()->json([
            'status' =>1,
            'message' => __('Lấy dữ liệu thành công'),
            'data' => $conversation,
        ]);

    }
    public function getSend($id){

         $order= Order::where('author_id', Auth::guard('api')->user()->id)
             ->select(['id','title'])
             ->find($id);

         $conversation=Conversation::where( 'ref_id' , $order->id)->first();
         if($conversation){
              $inbox=Inbox::with('user')->where('conversation_id',$conversation->id)->get();
         }
         else{
             $inbox=[];
         }

        return response()->json([
            'status'=>1,
            'message'=>'Thành công',
            'data'=>[
                'conversation'=>$conversation,
                'inbox'=>$inbox,
                'order'=>$order,
            ],

        ]);
    }

    public function postSend(Request $request,$id){

        if($request->filled('image') && count($request->image)>5){
            return redirect()->back()->withErrors('Bạn có thể upload tối đa 5 hình ảnh');
        };

        $this->validate($request, [
            'image.*' => 'mimes:jpg,jpeg,png,gif|max:10000',
            'message' => 'required',
        ], [
            'image.*.mimes' => 'Ảnh đính kèm không đúng định dạng jpg,jpeg,png,gif',
            'message.required' => 'Vui lòng nhập nội dung trao đổi',

        ]);

        $order=Order::where(function ($q){
            $q->orWhere('author_id',Auth::guard('api')->user()->id);
            $q->orWhere('processor_id',Auth::guard('api')->user()->id);
        })->find($id);
        if(!$order){
            return response()->json([
                'status'=>0,
                'message'=>'Không tìm thấy thông tin đơn hàng ',
            ]);
        }

        if($order->module== config('module.service-purchase.key')){
            $type_conversation=1;
        }
       else{
           $type_conversation=0;
       }

        $conversation=Conversation::where('ref_id',$order->id)->first();

        if($conversation){

            $conversation->author_id=$order->author_id;

        }else{

            $conversation=Conversation::create([
                'conversation_id' => $id,
                'author_id'=>$order->author_id,
                'type'=>$type_conversation,
                'ref_id'=>$order->id
            ]);

        }

        if($request->complain==1){
            $conversation->complain=1;
        }
        $conversation->save();

        $image="";
        if($request->hasFile('image')){
            //upload image
            $input['image'] = MediaHelpers::upload_image($request->image,'upload_client',null,null,null,false);
//            $input['image'] = Files::upload_image($request->image);
        }

        Inbox::create([
            'user_id'=>Auth::guard('api')->user()->id,
            'message'=>$request->message,
            'image'=>$image,
            'conversation_id'=>$conversation->id,
            'seen'=>"\"".Auth::guard('api')->user()->id."\"|"

        ]);
        return response()->json([
            'status'=>1,
            'message'=>'Gửi tin nhắn thành công',
        ]);
    }

    public function postSeen($id){


        $conversation=Conversation::where(function ($q){
            $q->orWhere('buyer',Auth::guard('api')->user()->id);
            $q->orWhere('author',Auth::guard('api')->user()->id);
        })->findOrFail($id);

        $seen="\"".Auth::guard('api')->user()->id."\"|";
        Inbox::where('conversation_id',$conversation->id)->where('seen', 'NOT LIKE', '%'.$seen.'%')->update([
            'seen'=>DB::raw("CONCAT(seen, '".$seen."')")
        ]);

        return  redirect()->to('/inbox/'.$conversation->conversation_id.'/send');

    }


}
