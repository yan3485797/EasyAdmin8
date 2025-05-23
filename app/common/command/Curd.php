<?php

namespace app\common\command;

use app\admin\service\console\CliEcho;
use app\admin\service\curd\BuildCurd;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Exception;

class Curd extends Command
{
    protected function configure()
    {
        $this->setName('curd')
            ->addOption('table', 't', Option::VALUE_REQUIRED, '主表名', null)
            ->addOption('controllerFilename', 'c', Option::VALUE_REQUIRED, '控制器文件名', null)
            ->addOption('modelFilename', 'm', Option::VALUE_REQUIRED, '主表模型文件名', null)
            #
            ->addOption('checkboxFieldSuffix', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '复选框字段后缀', null)
            ->addOption('radioFieldSuffix', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '单选框字段后缀', null)
            ->addOption('imageFieldSuffix', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '单图片字段后缀', null)
            ->addOption('imagesFieldSuffix', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '多图片字段后缀', null)
            ->addOption('fileFieldSuffix', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '单文件字段后缀', null)
            ->addOption('filesFieldSuffix', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '多文件字段后缀', null)
            ->addOption('dateFieldSuffix', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '时间字段后缀', null)
            ->addOption('switchFields', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '开关的字段', null)
            ->addOption('selectFields', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '下拉的字段', null)
            ->addOption('editorFields', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '富文本的字段', null)
            ->addOption('sortFields', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '排序的字段', null)
            ->addOption('ignoreFields', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '忽略的字段', null)
            #
            ->addOption('relationTable', 'r', Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '关联表名', null)
            ->addOption('foreignKey', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '关联外键', null)
            ->addOption('primaryKey', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '关联主键', null)
            ->addOption('relationModelFilename', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '关联模型文件名', null)
            ->addOption('relationOnlyFields', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '关联模型中只显示的字段', null)
            ->addOption('relationBindSelect', null, Option::VALUE_REQUIRED | Option::VALUE_IS_ARRAY, '关联模型中的字段用于主表外键的表单下拉选择', null)
            #
            ->addOption('force', 'f', Option::VALUE_REQUIRED, '强制覆盖模式', 0)
            ->addOption('delete', 'd', Option::VALUE_REQUIRED, '删除模式', 0)
            ->setDescription('一键curd命令服务');
    }

    protected function execute(Input $input, Output $output)
    {

        $table              = $input->getOption('table');
        $controllerFilename = $input->getOption('controllerFilename');
        $modelFilename      = $input->getOption('modelFilename');

        $checkboxFieldSuffix = $input->getOption('checkboxFieldSuffix');
        $radioFieldSuffix    = $input->getOption('radioFieldSuffix');
        $imageFieldSuffix    = $input->getOption('imageFieldSuffix');
        $imagesFieldSuffix   = $input->getOption('imagesFieldSuffix');
        $fileFieldSuffix     = $input->getOption('fileFieldSuffix');
        $filesFieldSuffix    = $input->getOption('filesFieldSuffix');
        $dateFieldSuffix     = $input->getOption('dateFieldSuffix');
        $switchFields        = $input->getOption('switchFields');
        $selectFields        = $input->getOption('selectFields');
        $sortFields          = $input->getOption('sortFields');
        $ignoreFields        = $input->getOption('ignoreFields');

        $relationTable         = $input->getOption('relationTable');
        $foreignKey            = $input->getOption('foreignKey');
        $primaryKey            = $input->getOption('primaryKey');
        $relationModelFilename = $input->getOption('relationModelFilename');
        $relationOnlyFields    = $input->getOption('relationOnlyFields');
        $relationBindSelect    = $input->getOption('relationBindSelect');

        $force  = $input->getOption('force');
        $delete = $input->getOption('delete');

        $relations = [];
        foreach ($relationTable as $key => $val) {
            $relations[] = [
                'table'              => $val,
                'foreignKey'         => $foreignKey[$key] ?? null,
                'primaryKey'         => $primaryKey[$key] ?? null,
                'modelFilename'      => $relationModelFilename[$key] ?? null,
                'onlyField'          => isset($relationOnlyFields[$key]) ? explode(",", $relationOnlyFields[$key]) : [],
                'relationBindSelect' => $relationBindSelect[$key] ?? null,
            ];
        }

        if (empty($table)) {
            if (PHP_SAPI == 'cli')
                CliEcho::error('请设置主表');
            else
                $output->writeln('请设置主表');
            return false;
        }

        try {
            $build = (new BuildCurd())
                ->setTable($table)
                ->setForce($force);

            !empty($controllerFilename) && $build = $build->setControllerFilename($controllerFilename);
            !empty($modelFilename) && $build = $build->setModelFilename($modelFilename);

            !empty($checkboxFieldSuffix) && $build = $build->setCheckboxFieldSuffix($checkboxFieldSuffix);
            !empty($radioFieldSuffix) && $build = $build->setRadioFieldSuffix($radioFieldSuffix);
            !empty($imageFieldSuffix) && $build = $build->setImageFieldSuffix($imageFieldSuffix);
            !empty($imagesFieldSuffix) && $build = $build->setImagesFieldSuffix($imagesFieldSuffix);
            !empty($fileFieldSuffix) && $build = $build->setFileFieldSuffix($fileFieldSuffix);
            !empty($filesFieldSuffix) && $build = $build->setFilesFieldSuffix($filesFieldSuffix);
            !empty($dateFieldSuffix) && $build = $build->setDateFieldSuffix($dateFieldSuffix);
            !empty($switchFields) && $build = $build->setSwitchFields($switchFields);
            !empty($selectFields) && $build = $build->setselectFields($selectFields);
            !empty($sortFields) && $build = $build->setSortFields($sortFields);
            !empty($ignoreFields) && $build = $build->setIgnoreFields($ignoreFields);

            foreach ($relations as $relation) {
                $build = $build->setRelation($relation['table'], $relation['foreignKey'], $relation['primaryKey'], $relation['modelFilename'], $relation['onlyField'], $relation['relationBindSelect']);
            }

            $build    = $build->render();
            $fileList = $build->getFileList();

            if (!$delete) {
                $result = $build->create();
                if ($force) {
                    if (PHP_SAPI == 'cli') {
                        $output->info(">>>>>>>>>>>>>>>");
                        foreach ($fileList as $key => $val) {
                            $output->info($key);
                        }
                        $output->info(">>>>>>>>>>>>>>>");
                        $output->info("确定强制生成上方所有文件? 如果文件存在会直接覆盖。 请输入 'yes' 按回车键继续操作: ");
                        $line = fgets(defined('STDIN') ? STDIN : fopen('php://stdin', 'r'));
                        if (trim($line) != 'yes') {
                            throw new Exception("取消文件CURD生成操作");
                        }
                        CliEcho::success('自动生成CURD成功');
                    }else {
                        $output->writeln('自动生成CURD成功');
                    }
                }
            }else {
                if (PHP_SAPI == 'cli') {
                    $output->info(">>>>>>>>>>>>>>>");
                    foreach ($fileList as $key => $val) {
                        $output->info($key);
                    }
                    $output->info(">>>>>>>>>>>>>>>");
                    $output->info("确定删除上方所有文件?  请输入 'yes' 按回车键继续操作: ");
                    $line = fgets(defined('STDIN') ? STDIN : fopen('php://stdin', 'r'));
                    if (trim($line) != 'yes') {
                        throw new Exception("取消删除文件操作");
                    }
                    $result = $build->delete();
                    CliEcho::success('>>>>>>>>>>>>>>>');
                    CliEcho::success('删除自动生成CURD文件成功');
                    CliEcho::success('>>>>>>>>>>>>>>>');
                    foreach ($result as $vo) {
                        CliEcho::success($vo);
                    }
                }else {
                    $result = $build->delete();
                    $output->writeln('>>>>>>>>>>>>>>>');
                    $output->writeln('删除自动生成CURD文件成功');
                    $output->writeln('>>>>>>>>>>>>>>>');
                    foreach ($result as $vo) {
                        $output->writeln($vo);
                    }
                }
            }
            if (PHP_SAPI == 'cli') {
                $output->info(">>>>>>>>>>>>>>>");
                $output->info('执行成功');
            }else {
                $output->writeln('执行成功');
            }
        }catch (\Exception $e) {
            if (PHP_SAPI == 'cli')
                CliEcho::error($e->getMessage());
            else
                $output->writeln($e->getMessage());
        }
        return false;
    }


}