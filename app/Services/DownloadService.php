<?php
declare(strict_types=1);

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DownloadService
{
    /**
     * ダウンロード
     * 
     * @param array $guests
     * @param string $addFileName
     * @return void
     */
    public function download(array $guests, string $addFileName = '')
    {
        $spreadsheet = new Spreadsheet();
        
        //全体のフォント設定
        $spreadsheet->getDefaultStyle()->getFont()->setName('游ゴシック');
        
        //処理したいシートを取得
        $sheet = $spreadsheet->getActiveSheet();
        
        
        //書き込みデータを準備
        $writeDatas = [['会社名', '名前', 'ふりがな', '年齢', 'メールアドレス', '配信用メールアドレス']];
        
        foreach ($guests as $guest) {
            $writeDatas[] = [
                $guest->company->company_name,
                $guest->name,
                $guest->name_kana,
                $guest->age,
                $guest->email,
                $guest->stream_email
            ];
        }

        $sheet->fromArray($writeDatas, null, 'A1');
        
        for ($i = 'A'; $i != 'G'; $i++) {
            
            //セル幅自動調整
            $sheet->getColumnDimension($i)->setAutoSize(true);
            
            //太字
            $sheet->getStyle($i.'1')->getFont()->setBold(true)->setSize(10);
        }
        
        //ファイル名を設定
        $fileName = '第'.$guests[0]->event[0]->times.'回交流会予約者一覧'.$addFileName.'.xlsx';

        //Excelファイルをダウンロード
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
