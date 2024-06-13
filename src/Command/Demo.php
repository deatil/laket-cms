<?php

declare (strict_types = 1);

namespace Laket\Admin\CMS\Command;

use think\console\Input;
use think\console\Output;
use think\console\Command;

use Laket\Admin\Facade\Flash;

/**
 * 添加 Demo 数据
 *
 * > php think laket-cms:demo
 *
 * @create 2024-6-13
 * @author deatil
 */
class Demo extends Command
{
    /**
     * 配置
     */
    protected function configure()
    {
        $this
            ->setName('laket-cms:demo')
            ->setDescription('You will install demo data.');
    }

    /**
     * 执行
     */
    protected function execute(Input $input, Output $output)
    {
        $output->newLine();
        
        $password = $this->output->ask($input, '> You will install demo data (Y/n)?', 'y');
        if (empty($password)) {
            $output->error('> You don\'t install demo data! ');
            return false;
        }
        
        // 数据库
        Flash::executeSql(__DIR__ . '/../../resources/database/demo.sql');
        
        $output->info('Install demo data successfully!');
    }

}
