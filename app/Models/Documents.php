<?php

namespace App\Models;

use Creagia\LaravelSignPad\Concerns\RequiresSignature;
use Creagia\LaravelSignPad\Contracts\CanBeSigned;
use Creagia\LaravelSignPad\Contracts\ShouldGenerateSignatureDocument;
use Creagia\LaravelSignPad\SignatureDocumentTemplate;
use Creagia\LaravelSignPad\SignaturePosition;
use Creagia\LaravelSignPad\Templates\PdfDocumentTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Documents extends Model implements CanBeSigned, ShouldGenerateSignatureDocument
{
    use HasFactory, RequiresSignature;

    protected $fillable = [
        'path',
        'name',
        'file_path',
        'user_id',
    ];

    public function getSignatureDocumentTemplate(): SignatureDocumentTemplate
    {
        return new SignatureDocumentTemplate(
            outputPdfPrefix: 'document',

            template: new PdfDocumentTemplate(storage_path('pdf/template.pdf')),
            signaturePositions: [
                new SignaturePosition(
                    signaturePage: 1,
                    signatureX: 20,
                    signatureY: 25,
                ),
                new SignaturePosition(
                    signaturePage: 2,
                    signatureX: 25,
                    signatureY: 50,
                ),
            ]
        );
    }

    public function DlFront(Request $req)
    {
        $previousUrl = URL()->previous();
        if (preg_match("/\/(\d+)$/", $previousUrl, $matches)) {
            $user_id = $matches[1];
        } else {
            // Your URL didn't match.  This may or may not be a bad thing.
        }

        $req->validate([
            'file' => 'required|mimes:csv,txt,xlx,xls,pdf,jpeg,jpg,png|max:65536',
        ]);
        $fileModel = new File;
        if ($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->user_id = $user_id;
            $fileModel->document_type = 'Driving Licence Front';
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            return to_route('users.show', [$user_id])
                ->with('success', 'The front of the driving licence has been uploaded.');
        }
    }
}
