<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

class LogViewerController extends Controller
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var LaravelLogViewer
     */
    private $log_viewer;

    /**
     * @var string
     */
    protected $view_log = 'laravel-log-viewer::log';

    /**
     * LogViewerController constructor.
     */
    public function __construct()
    {
        $this->log_viewer = new LaravelLogViewer();
        $this->request = app('request');
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function index(Request $request)
    {

        $page_title = 'Dashboard';

        $page_breadcrumbs = [
            [   'page' => '1',
                'title' => 'Home',
            ],
        ];

        if ($this->request->input('l')) {
            $this->log_viewer->setFile(Crypt::decrypt($this->request->input('l')));
        }

        if ($early_return = $this->earlyReturn()) {
            return $early_return;
        }


        $data = [
            'logs' => $this->log_viewer->all(),
            'files' => $this->log_viewer->getFiles(true),
            'current_file' => $this->log_viewer->getFileName(),
            'standardFormat' => true,
            'storage_path' => $this->log_viewer->getStoragePath(),
        ];

        if ($this->request->wantsJson()) {
            return $data;
        }

        if (is_array($data['logs']) && count($data['logs']) > 0) {
            $firstLog = reset($data['logs']);
            if (!$firstLog['context'] && !$firstLog['level']) {
                $data['standardFormat'] = false;
            }
        }
        if($request->ajax())
        {
            $f = $request->hasFile('f');

            return $f;
        }

        ActivityLog::add($request, 'Truy cập Logs error');

        return view('admin.logs-view.index', compact('page_title', 'page_breadcrumbs'))->with('data',$data);
    }

    public function feach_data(Request $request)
    {

        $page_title = 'Dashboard';
        $page_breadcrumbs = [
            [   'page' => '1',
                'title' => 'Home',
            ],
        ];

        if($request->ajax())
        {
            $f = $request->hasFile('f');

            return $f;
        }


    }

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    private function earlyReturn()
    {
        if ($this->request->input('f')) {
            $this->log_viewer->setFolder(Crypt::decrypt($this->request->input('f')));
        }

        if ($this->request->input('dl')) {
            return $this->download($this->pathFromInput('dl'));
        } elseif ($this->request->has('clean')) {
            app('files')->put($this->pathFromInput('clean'), '');
            return $this->redirect(url()->previous());
        } elseif ($this->request->has('del')) {
            app('files')->delete($this->pathFromInput('del'));
            return $this->redirect($this->request->url());
        } elseif ($this->request->has('delall')) {
            $files = ($this->log_viewer->getFolderName())
                ? $this->log_viewer->getFolderFiles(true)
                : $this->log_viewer->getFiles(true);
            foreach ($files as $file) {
                app('files')->delete($this->log_viewer->pathToLogFile($file));
            }
            return $this->redirect($this->request->url());
        }
        return false;
    }

    /**
     * @param string $input_string
     * @return string
     * @throws \Exception
     */
    private function pathFromInput($input_string)
    {
        return $this->log_viewer->pathToLogFile(Crypt::decrypt($this->request->input($input_string)));
    }

    /**
     * @param $to
     * @return mixed
     */
    private function redirect($to)
    {
        if (function_exists('redirect')) {
            return redirect($to);
        }

        return app('redirect')->to($to);
    }

    /**
     * @param string $data
     * @return mixed
     */
    private function download($data)
    {
        if (function_exists('response')) {
            return response()->download($data);
        }

        // For laravel 4.2
        return app('\Illuminate\Support\Facades\Response')->download($data);
    }
}
