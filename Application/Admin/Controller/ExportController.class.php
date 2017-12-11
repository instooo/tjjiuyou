<?php
/**
 * 数据导出类
 * Created by qinfan.
 * Date: 2016/10/26
 */
namespace Admin\Controller;
use Think\Controller;
use Think\Model\AdvModel;

class ExportController extends CommonController {
    public $objPHPExcel;
    public $chars = array (1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J',
        11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N',  15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T',
        21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z', 27 => 'AA', 28 => 'AB', 29 => 'AC', 30 => 'AD',
        31 => 'AE', 32 => 'AF', 33 => 'AG', 34 => 'AH', 35 => 'AI', 36 => 'AJ', 37 => 'AK', 38 => 'AL', 39 => 'AM', 40 => 'AN',
        41 => 'AO', 42 => 'AP', 43 => 'AQ', 44 => 'AR',45 => 'AS',46=> 'AT', 47 => 'AU', 48 => 'AV', 49 => 'AW', 50 => 'AX', 51 => 'AY', 52 => 'AZ', 53 => 'BA', 54 => 'BB', 55 => 'BC',
        56 => 'BD', 57 => 'BE', 58 => 'BF', 59 => 'BG',60 => 'BH',61=> 'BI',
    );

    public function __construct() {
        parent::__construct();
        //excel初始化
        $this->init();
    }

    //初始化
    public function init() {
        vendor('PHPExcel.PHPExcel');
        $this->objPHPExcel = new \PHPExcel();
        $this->objPHPExcel->getProperties()
            ->setCreator('7477 Inc')
            ->setLastModifiedBy('7477 Inc')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
    }

    //创建并导出文档
    public function createDocument($filename) {
        if (!$this->objPHPExcel) return false;
        vendor('PHPExcel.PHPExcel.IOFactory');
        $this->objPHPExcel->getActiveSheet()->setTitle ($filename);
        $this->objPHPExcel->setActiveSheetIndex(0);
        $this->objPHPExcel->setActiveSheetIndex(0);
        $objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');

        ob_end_clean ();
        header ( "Pragma: public" );
        header ( "Expires: 0" );
        header ( "Cache-Control:must-revalidate,post-check=0,pre-check=0" );
        header ( "Content-Type:application/force-download" );
        header ( "Content-Type:application/vnd.ms-execl" );
        header ( "Content-Type:application/octet-stream" );
        header ( "Content-Type:application/download" );
        header ( 'Content-Disposition:attachment;filename=' . $filename . '.xlsx' );
        header ( "Content-Transfer-Encoding:binary" );

        $objWriter->save ( 'php://output' );
        exit ();

    }

    public function test() {
        $this->objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '游戏推广回款总汇:')
            ->setCellValue('A2', '媒 体')
            ->setCellValue('B2', '一级媒体')
            ->setCellValue('C2', '二级媒体')
            ->setCellValue('D2', '总回款')
            ->setCellValue('E2', '本月回款')
            ->setCellValue('F2', '成 本')
            ->setCellValue('G2', '时 间')
            ->setCellValue('H2', '当月回款率')
            ->setCellValue('I2', '游 戏 回 款 明 细');
        $this->createDocument('aaa');
    }

}