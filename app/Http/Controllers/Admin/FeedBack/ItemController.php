<?php

namespace App\Http\Controllers\Admin\FeedBack;

use App\Models\FeedBack;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $page_breadcrumbs;
    protected $module;
    protected $moduleCategory;

    public function __construct(Request $request)
    {

        $this->module='feedback';
        $this->moduleCategory=null;
        //set permission to function
        $this->middleware('permission:'. $this->module.'-list');
            $this->middleware('permission:'. $this->module.'-create', ['only' => ['create', 'store','duplicate']]);
        $this->middleware('permission:'. $this->module.'-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:'. $this->module.'-delete', ['only' => ['destroy']]);

        if( $this->module!=""){
            $this->page_breadcrumbs[] = [
                'page' => route('admin.'.$this->module.'-list'),
                'title' => "Tất cả ý kiến"
            ];
        }
    }

    public function  createFeebBack(Request $request){
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Hòm thư góp ý")
        ];
        $dataCate = Group::where("module","feedback-config")->where("status",1)->get();
        return view('admin.'.$this->module.'.form-feedback')
            ->with('module', $this->module)
            ->with('dataCate', $dataCate)
            ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function feedbackList(Request $request)
    {
        ActivityLog::add($request, 'Truy cập danh sách '.$this->module);
        if($request->ajax) {
            $datatable= FeedBack::where('status','<>','999')->where('type','<>','0');

            if ($request->filled('qtv'))  {
                $author = User::where('username', 'LIKE', '%' . $request->get('qtv') . '%')->first();
                if($author) {
                    $datatable->where('author_id', $author->id);
                }
            }

            if(!auth()->user()->can($this->module.'-list-all')){
                $author = User::where('id', auth()->user()->id)->first();
                if($author) {
                    $datatable->where('author_id', $author->id);
                }
            }

            if ($request->filled('title'))  {
                $datatable->where('title', 'LIKE', '%' . $request->get('title') . '%');
            }
            if ($request->filled('type'))  {
                $datatable->where('type',$request->get('type'));
            }
            if ($request->filled('title'))  {
                $datatable->where(function($q) use($request){
                    $q->orWhere('title', 'LIKE', '%' . $request->get('title') . '%');
                });
            }
            if ($request->filled('author'))  {
                $author =  User::where('username',$request->get('author'))->first();
                if($author){
                    $datatable->where('author_id',$author->id);
                }
            }
            if ($request->filled('status')) {
                $datatable->where('status',$request->get('status') );
            }
            else{
                $datatable->where('status','<>',0);
            }

            return \datatables()->eloquent($datatable)
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y H:i:s', strtotime($data->created_at));
                })
                ->editColumn('contents', function($data) {
                    return strip_tags($data->contents);
                })
                ->editColumn('author_name', function($data) {
                    $author = User::where('id',$data->author_id)->first();
                    if($author){
                        return $author->username;
                    }
                    else{
                        return "";
                    }
                })
                ->addColumn('type', function($data) {
                    $type = Group::where("id",$data->type)->first();
                    if($type){
                        $temp = "<span class=\"label label-pill label-inline label-center mr-2  label-success \" style=\"background-color: #".$type->slug."!important\">" . $type->title . "</span>";
                    }
                    else{
                        $temp = "<span class=\"label label-pill label-inline label-center mr-2  label-success \" style=\"background-color: #f00!important\">Chưa chọn loại</span>";

                    }
                    return $temp;
                })
                ->addColumn('action', function($row) {
                    //Count
                    $subComment = FeedBack::where('type',0)->where('parent_id',$row->id)->get()->count();
                    if($row->author_id != auth()->user()->id) {
                        $subComment_unread = 0 + $row->au_comment_un_read;
                    }
                    else{
                        $subComment_unread = 0 + $row->un_read;
                    }
                    $temp = "<a title='Tổng số comment' style='position:relative;z-index:1' href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Comment\"><i class=\"la la-comment\"></i><span class='count' style='position: absolute;top: -4px;left: 18px;color: #fff;font-size: 10px;font-weight: bold;background: blue;padding: 2px;border-radius: 100px;min-width: 18px;text-align: center;'>".$subComment."</span></a>";
                    $temp .= "<a title='Số comment chưa đọc' style='position:relative;z-index:1' href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Comment\"><i class=\"la la-envelope\"></i><span class='count' style='position: absolute;top: -4px;left: 18px;color: #fff;font-size: 10px;font-weight: bold;background: red;padding: 2px;border-radius: 100px;min-width: 18px;text-align: center;'>".$subComment_unread."</span></a>";
                    if($row->seen == 1) {
                        $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger view_toggle' data-toggle=\"modal\" data-target=\"#viewModal\" class=\"view_toggle\" title=\"Góp ý đã được admin xem. Ấn để xem nội dung\"><i class=\"fa fa-eye\" style='color:#00a651 '></i></a>";
                    }
                    else{
                        $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger view_toggle' data-toggle=\"modal\" data-target=\"#viewModal\" class=\"view_toggle\" title=\"Góp ý chưa được admin xem. Ấn để xem nội dung\"><i class=\"fa fa-eye-slash\"></i></a>";
                    }
                    //$temp .= "<a href=\"".route('admin.'.$this->module.'.edit',$row->id)."\"  rel=\"$row->id\" class=\"btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary \" title=\"Sửa\"><i class=\"la la-edit\"></i></a>";
                    if($row->status == 1 && $row->author_id == auth()->user()->id) {
                        $temp .= "<a  rel=\"$row->id\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle=\"modal\" data-target=\"#deleteModal\" class=\"delete_toggle\" title=\"Xóa\"><i class=\"la la-trash\"></i></a>";
                    }
                    return $temp;
                })->rawColumns(["type","action"])
                ->toJson();
        }
        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Danh sách hòm thư")
        ];
        $dataCate = Group::where("module","feedback-config")->where("status",1)->get();
        return view('admin.'.$this->module.'.index')
        ->with('module', $this->module)
        ->with('dataCate', $dataCate)
        ->with('page_breadcrumbs', $this->page_breadcrumbs);
    }

    public function post_feedback(Request $request)
    {
        $this->validate($request,[
            'type'=>'required',
            'title'=>'required',
        ],[
            'type.required' => __('Vui lòng chọn mục góp ý'),
            'title.required' => __('Vui lòng nhập tiêu đề'),
        ]);
        $input = [
            'author_id' =>auth()->user()->id,
            'type' => $request->type,
            'title' => $request->title,
            'contents' => $request->contents,
            'status'=> 1,
            'seen' => 0,
            'files'=> $request->get('files')

        ];

        FeedBack::create($input);
        if($request->filled('submit-close')){
            return redirect()->back()->with('success',__('Cảm ơn bạn đã đóng góp ý kiến, Chúng tôi sẽ liên hệ trong thời gian sớm nhất!'));
        }
        else {
            return redirect()->back()->with('success',__('Cảm ơn bạn đã đóng góp ý kiến, Chúng tôi sẽ liên hệ trong thời gian sớm nhất!'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $input=explode(',',$request->id);
        $data =  FeedBack::where("id",$input)->first();
        if($data) {
            $data->status = 0;
            $data->save();
            ActivityLog::add($request, 'Xóa thành công ' . $this->module . ' #' . json_encode($input));
            return redirect()->back()->with('success', __('Xóa thành công !'));
        }
        else{
            return redirect()->back()->withErrors(__('Xóa thất bại, không tồn tại ID xóa !'));
        }
    }

    public function edit(Request $request, $id)
    {

        $this->page_breadcrumbs[] =[
            'page' => '#',
            'title' => __("Cập nhật"),

        ];
        $data = FeedBack::findOrFail($id);
        if(!auth()->user()->can($this->module.'-list-all')){
            if($data->author_id !=  auth()->user()->id) {
                return redirect()->back()->withErrors(__('Bạn không có quyền xem bản ghi này!'));
            }
        }
        if($data->status ==0){
            return redirect()->back()->withErrors(__('Bạn không thể xem bản ghi đã bị xóa!'));
        }
        //update view
        if($data->author_id != auth()->user()->id) {
            $data->seen = 1;
            $data->save();

        }
        //Update view comment
        if($data->author_id == auth()->user()->id) {
            $lstComment = FeedBack::where('type',0)->where('parent_id',$id)->where('seen',0)->whereRaw('author_comment_id is not null')->pluck('id')->toArray();
            DB::table('feedback')->whereIn('id', $lstComment)->update(['seen' => 1]);
            $data->un_read = 0;
            $data->save();

        }
        else{
            $lstComment = FeedBack::where('type',0)->where('parent_id',$id)->where('seen',0)->whereRaw('author_comment_id is null')->pluck('id')->toArray();
            DB::table('feedback')->whereIn('id', $lstComment)->update(['seen' => 1]);
            $data->au_comment_un_read = 0;
            $data->save();
        }


        $dataCate = Group::where("module","feedback-config")->where("status",1)->get();
        ActivityLog::add($request, 'Vào form edit '.$this->module.' #'.$data->id);
        return view('admin.'.$this->module.'.create_edit')
            ->with('module', $this->module)
            ->with('dataCate', $dataCate)
            ->with('page_breadcrumbs', $this->page_breadcrumbs)
            ->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data =  FeedBack::findOrFail($id);
//        if ($data->author_id != auth()->user()->id){
//            $this->validate($request, [
//                'status' => 'required'
//            ], [
//                'status.required' => __('Vui lòng chọn trạng thái')
//            ]);
//        }
//        if($data->status !=1){
//            return redirect()->back()->withErrors(__('Ý kiến đang được xử lý, Không thể cập nhật lúc này!'));
//        }
        if($data->author_id == auth()->user()->id)
        {
            if($data->status == 1){
                $input = [
                    'type' => $request->type,
                    'title' => $request->title,
                    'contents' => $request->contents,
                    'status' => $request->status,
                    'files' => $request->get('files')
                ];
            }
            else {
                $input = [
                    'type' => $request->type,
                    'title' => $request->title,
                    'contents' => $request->contents,
                    'files' => $request->get('files')
                ];
            }
        }
        else {
            $input = [
                'status' => $request->status
            ];
        }
        $data->update($input);
        ActivityLog::add($request, 'Cập nhật thành công ý kiến '.$this->module.' #'.$data->id);
        if($request->filled('submit-close')){
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
        else {
            return redirect()->back()->with('success',__('Cập nhật thành công !'));
        }
    }


    public function  get_Comment(Request $request){

        $comment = FeedBack::select('feedback.*','a.username as author','b.username as author_comment')->where('feedback.status',1)->leftJoin('users as a','a.id','feedback.author_id')->leftJoin('users as b','b.id','feedback.author_comment_id')->where('parent_id',$request->idComment)->orderBy("created_at","desc")->get();
        return response()->json(array('status' => "SUCCESS","msg"=>"Đã load","data"=> $comment), 200);
    }

    public function  post_Comment(Request $request){
        $idComment = $request->idComment;
        $content = $request->content;
        $comment = FeedBack::findOrFail($idComment);

        //Update view comment
        if($comment->author_id == auth()->user()->id) {
            $lstComment = FeedBack::where('type',0)->where('parent_id',$idComment)->where('seen',0)->whereRaw('author_comment_id is not null')->pluck('id')->toArray();
            DB::table('feedback')->whereIn('id', $lstComment)->update(['seen' => 1]);
        }
        else{
            $lstComment = FeedBack::where('type',0)->where('parent_id',$idComment)->where('seen',0)->whereRaw('author_comment_id is null')->pluck('id')->toArray();
            DB::table('feedback')->whereIn('id', $lstComment)->update(['seen' => 1]);
        }


        if($comment->author_id == auth()->user()->id){
            $input = [
                'author_id' =>auth()->user()->id,
                'parent_id' =>$idComment,
                'contents' => $content,
                'status'=> 1,
                'seen' => 0,
                'type' =>0
            ];
            $comment->au_comment_un_read = $comment->au_comment_un_read +1;
            $comment->save();
        }
        else{
            $input = [
                'author_comment_id' =>auth()->user()->id,
                'parent_id' =>$idComment,
                'contents' => $content,
                'status'=> 1,
                'seen' => 0,
                'type' =>0
            ];
            $comment->un_read = $comment->un_read +1;
            $comment->save();
        }

        FeedBack::create($input);
        ActivityLog::add($request, 'Đã trả lời ý kiến #'.$idComment);
        return response()->json(array('status' => "SUCCESS","msg"=>"Đã trả lời ý kiến #".$idComment), 200);
    }

    public function  getInfoFeedBack(Request $request){
//        $id = $request->id;
//        $comment = FeedBack::findOrFail($id);
//        if($comment->author_id != auth()->user()->id) {
//            $comment->seen = 1;
//            $comment->save();
//        }
//        $data = $comment->contents;
        return response()->json(array('status' => "SUCCESS","msg"=>"Thành công", "data" =>111111111111), 200);
    }

    public function  countComment(Request $request){
        $fb = FeedBack::where('status','<>','999')->where('status','<>','0')->where('type','<>','0');
        if(!auth()->user()->can($this->module.'-list-all')){
            $author = User::where('id', auth()->user()->id)->first();
            if($author) {
                $fb->where('author_id', $author->id);
            }
        }
        $fb = $fb->get();
        $subComment_unread = 0;
        if(isset($fb)){
            foreach ($fb as $row){
                if($row->author_id != auth()->user()->id) {
                    $subComment_unread = $subComment_unread + $row->au_comment_un_read;//FeedBack::where('type',0)->where('seen',0)->whereRaw('author_id is not null')->where('parent_id',$row->id)->get()->count();
                }
                else{
                    $subComment_unread =$subComment_unread +  $row->un_read;//FeedBack::where('type',0)->where('seen',0)->whereRaw('author_id is null')->where('parent_id',$row->id)->get()->count();
                }
            }
        }

        return response()->json(array('status' => "SUCCESS","msg"=>"Thành công", "data" =>$subComment_unread), 200);
    }


}
