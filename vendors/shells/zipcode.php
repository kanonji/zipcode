<?php
class ZipcodeShell extends Shell{
    const csv = 'ken_all.csv';
    const csvBulk = 'jigyosyo.csv';
    public $uses = array('Zipcode');
    
    public function main(){
        $this->out(__('Zipcode for japan Shell', true));
        $this->hr();
        $this->out(__('[I]nitialize zipcodes database table', true));
        $this->out(__('[D]ownload csv and save to zipcodes table', true));
        $this->out(__('[J]SON for AjaxZip(not yet)', true));
        $this->out(__('[H]elp', true));
        $this->out(__('[Q]uit', true));

        $choice = strtolower($this->in(__('What would you like to do?', true), array('I', 'D', 'J', 'H', 'Q')));
        switch ($choice) {
            case 'i':
                $this->initdb();
                break;
            case 'd':
                $this->download();
                break;
            case 'j':
                $this->json();
                break;
            case 'h':
                $this->help();
                break;
            case 'q':
                exit(0);
                break;
            default:
                $this->out(__('You have made an invalid selection. Please choose a command to execute by entering I, D, J, H, or Q.', true));
        }
        $this->hr();
        $this->main();
    }
    
    public function initdb(){
        if(! file_exists(CONFIGS.'schema/zipcodes.php'))
            exit('zipcodes.php is required at APP/config/schema/zipcodes.php');
        $this->Dispatch->args = array('schema', 'create', 'zipcodes');
        $this->Dispatch->dispatch();
    }
    
    public function download(){
        if(! shell_exec("whereis lha") && ! shell_exec("which lha")
            && (! file_exists(TMP.self::csv) || ! file_exists(TMP.self::csvBulk)))
            exit('No lha command found which this shell requires.');
        if(! file_exists(TMP.self::csv))
            $this->getCsv();
        if(! file_exists(TMP.self::csv))
            exit('Download or extract error');
        if(! file_exists(TMP.self::csvBulk))
            $this->getCsvBulk();
        if(! file_exists(TMP.self::csvBulk))
            exit('Download or extract error');
        $this->Zipcode->deleteAll('1 = 1', false);
        $this->save();
        $this->saveBulk();
    }
    
    public function json(){
        $this->out(__('Not implimented yet', 2));
    }
    
    public function help(){
        $this->hr();
        $this->out(__('Zipcode Shell:', true));
        $this->hr();
        $this->out(__('Description..................................', true));
        $this->out(__('.....................................Not yet.', true));
        $this->hr();
        $this->out(__('usage:', true));
        $this->out('   cake zipcode help');
        $this->out('   cake zipcode initdb [-datasource custom]');
        $this->out('   cake zipcode download');
        $this->out();
        $this->hr();
    }
    
    private function getCsv(){
        system("cd ".TMP."; php -r 'readfile(\"http://www.post.japanpost.jp/zipcode/dl/kogaki/lzh/ken_all.lzh\");' | lha x -");
    }
    
    private function getCsvBulk(){
        system("cd ".TMP."; php -r 'readfile(\"http://www.post.japanpost.jp/zipcode/dl/jigyosyo/lzh/jigyosyo.lzh\");' | lha x -");
    }
    
    private function save(){
        if(! $this->convertEncoding(TMP.self::csv))
            exit('Converting encode is failed');
        $fp = fopen(TMP.self::csv, 'r');
        while($recode = fgetcsv($fp)){
            echo memory_get_usage()." bytes memory used \n";
            $recode[8] = str_replace(array('以下に掲載がない場合', '（次のビルを除く）'), '', $recode[8]);
            $recode[5] = str_replace(array('ｲｶﾆｹｲｻｲｶﾞﾅｲﾊﾞｱｲ', '(ﾂｷﾞﾉﾋﾞﾙｦﾉｿﾞｸ)'), '', $recode[5]);
            $data = array(
                'id' => $recode[2],
                'prefecture' => $recode[6],
                'city' => $recode[7],
                'town' => $recode[8],
                'prefecture_ruby' => mb_convert_kana($recode[3]),
                'city_ruby' => mb_convert_kana($recode[4]),
                'town_ruby' => mb_convert_kana($recode[5]),
                'jiscode' => $recode[0],
            );
            $this->Zipcode->create();
            $this->Zipcode->save($data);
        }
        unlink(TMP.'ken_all.csv');
    }
    
    private function saveBulk(){
        if(! $this->convertEncoding(TMP.self::csvBulk))
            exit('Converting encode is failed');
        $fp = fopen(TMP.self::csvBulk, 'r');
        while($recode = fgetcsv($fp)){
            echo memory_get_usage()." bytes memory used \n";
            $data = array(
                'id' => $recode[7],
                'prefecture' => $recode[3],
                'city' => $recode[4],
                'town' => $recode[5],
                'block_number' => $recode[6],
                'jiscode' => $recode[0],
            );
        }
        $this->Zipcode->create();
        $this->Zipcode->save($data);
        unlink(TMP.'jigyosyo.csv');
    }
    
    private function convertEncoding($path){
        $flag = null;
        if(shell_exec("whereis nkf") || shell_exec("which nkf"))
            $flag = 'nkf';
        if(is_null($flag) && (shell_exec("whereis iconv") || shell_exec("which iconv")))
            $flag = 'iconv';
        if(is_null($flag) && (shell_exec("whereis kconv") || shell_exec("which kconv")))
            $flag = 'kconv';
        if(is_null($flag))
            $flag = 'php';
        switch($flag){
            case 'nkf':
                return $this->convertWithNkf($path);
                break;
            case 'iconv':
            case 'kconv':
                exit('Not implemented yet');
                break;
            case 'php':
                return $this->convertWithPHP($path);
                break;
        }
    }
    
    private function convertWithNkf($path){
        switch(mb_internal_encoding()){
            case 'UTF-8':
                $option = 'w';
                break;
            case 'EUC-JP':
                $option = 'e';
                break;
            case 'SJIS':
                $option = 's';
                break;
            default:
                exit('Internal encoding is not expected');
                break;
        }
        exec("nkf -{$option} --overwrite {$path}", $dummy, $returnValue);
        return !($returnValue);
    }
    
    private function convertWithPHP($path){
        if(! $str = file_get_contents($path))
            exit('File load error');
        mb_detect_order("ASCII, JIS, UTF-8, EUC-JP, SJIS");
        if(! $encoding = mb_detect_encoding($str))
            exit('Encoding can not be detected');
        if($encoding == mb_internal_encoding())
            return true;
        $str = mb_convert_encoding($str, mb_internal_encoding(), $encoding);
        return file_put_contents(TMP.'ken_all.csv', $str);
    }
}