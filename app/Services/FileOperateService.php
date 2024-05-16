<?php
declare(strict_types=1);

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class FileOperateService
{
    /**
     * ダウンロード
     * 
     * @param array $guests
     * @param int $times
     * @param string $addFileName
     * @return void
     */
    public function download(array $guests, int $times, string $addFileName = '')
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
        $fileName = '第'.$times.'回交流会予約者一覧'.$addFileName.'.xlsx';

        //Excelファイルをダウンロード
        $writer = new XlsxWriter($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
    
    /**
     * インポート
     * 
     * @param string $fileName
     * @return array
     */
    public function import(string $fileName)
    {
        //Excelファイルを読み込み
        $reader = new XlsxReader();
        
        //保存したファイル情報を取得
        $spreadsheet = $reader->load(storage_path().'/app/'.$fileName);
        
        $sheet = $spreadsheet->getSheet(0);

        //読み込んだデータを配列にする
        $sheetData = $sheet->toArray();
        
        //開催回を取得
        $times = $sheetData[0][0];
        
        //登録データのみにする
        unset($sheetData[0], $sheetData[1]);
        
        //登録用データを成型
        $insertData = [];
        foreach ($sheetData as $rowData) {
            $insertData[] = [
                'company_name' => $rowData[0],
                'name' => $rowData[1],
                'name_kana' => $rowData[2],
                'age' => $rowData[3],
                'email' => $rowData[4],
                'stream_email' => $rowData[5],
                'times' => $times,
                'capacityCheck' => 'on'
            ];
        }
        
        return $insertData;
    }
}
