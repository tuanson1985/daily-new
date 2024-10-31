<?php

namespace App\Jobs;

use App\Library\Helpers;
use App\Models\Media;
use App\Models\OrderDetail;
use App\Models\Shop;
use Cache;
use Carbon\Carbon;
use File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Illuminate\Support\Facades\Storage;

class DeleteImageNickCompleteS3Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $order_detail;
    public function __construct(OrderDetail $order_detail)
    {
        $this->order_detail = $order_detail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $order_detail = $this->order_detail;

            if (isset($order_detail->content)){
                $url = $order_detail->content;
                $base_url = config('module.media_s3');
                $path = str_replace($base_url, '', $url);
                // Kiểm tra xem tệp tồn tại trên S3 hay không
                if (Storage::disk('s3')->exists($path)) {
                    //Xóa ảnh
                    Storage::disk('s3')->delete($path);
                    //Lấy tên thư mục cha
                    $parentDirectory = dirname($path);
                    // Kiểm tra xem thư mục cha có rỗng không
                    $filesInParentDirectory = Storage::disk('s3')->files($parentDirectory);

                    $order_detail->content = null;
                    $order_detail->save();
                    // Nếu không còn file nào trong thư mục cha
                    if (empty($filesInParentDirectory)) {
                        // Xóa thư mục cha
                        Storage::disk('s3')->deleteDirectory($parentDirectory);
                    }
                }
            }

            return "Xóa image thành công";

        } catch (\Exception $e) {
            Log::error($e);
            return "Lỗi clear image:".$e->getMessage();
        }
    }
}
