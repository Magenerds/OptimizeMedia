<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Magenerds
 * @package    Magenerds_OptimizeMedia
 * @author     Mahmood Dhia <m.dhia@techdivision.com>
 * @copyright  Copyright (c) 2018 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 */

namespace Magenerds\OptimizeMedia\Setup;

use Magenerds\OptimizeMedia\Helper\OptimizeImage as OptimizeImageHelper;
use Magenerds\OptimizeMedia\Model\OptimizeImage as OptimizeImageModel;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable(OptimizeImageHelper::TABLENAME)
        )->addColumn(
            OptimizeImageModel::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Image id'
        )->addColumn(
            OptimizeImageModel::HASHED_PATH,
            Table::TYPE_TEXT,
            32,
            ['nullable' => false],
            'MD5 hashed relative path to image from magento root'
        )->addColumn(
            OptimizeImageModel::PATH,
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Contains the relative path to image from magento root'
        )->addColumn(
            OptimizeImageModel::MODIFY_TIME,
            Table::TYPE_INTEGER,
            12,
            ['nullable' => true, 'unsigned' => true],
            'Contains the last file modify'
        )->addColumn(
            OptimizeImageModel::MD5,
            Table::TYPE_TEXT,
            32,
            ['nullable' => true],
            'Contains file MD5 hash'
        )->addColumn(
            OptimizeImageModel::CRC32,
            Table::TYPE_TEXT,
            8,
            ['nullable' => true],
            'Contains file crc32 checksum'
        )->addIndex(
            $setup->getIdxName(OptimizeImageHelper::TABLENAME, [OptimizeImageModel::HASHED_PATH], AdapterInterface::INDEX_TYPE_UNIQUE),
            [OptimizeImageModel::HASHED_PATH], ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'OptimizeImage Table'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}