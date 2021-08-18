<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Dropfile;

class DropFileController extends Controller
{
    public function __construct()
    {
        $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
    }
    public function index()
    {
        $files =  Dropfile::all();
        return view('pages.drop-index',compact('files'));
    }
    public function strore()
    {
        try {
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach ($files as $file ) {
                    $fileExtension = $file->getClientOriginalExtension();
                    $mimeType = $file->getClientMimeType();
                    $fileSize = $file->getClientSize();
                    $newName = uniqid().'.'.$fileExtension;

                    Storage::disk('dropbox')->putFileAs('public/upload',$file,$newName);
                    $this->dropbox->createSharedLinkWithSettings('public/upload'.$newName);

                    Dropfile::create([
                        'file_title' => $newName,
                        'file_type' => $mimeType,
                        'file_size' =>$fileSize
                    ]);

                    return redirect('drop');
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }
    public function show()
    {
        
    }
}
