<?
use Bitrix\Main\ORM;

class ElementPropertyTable extends ORM\Data\DataManager
{
    /**
     * @return string|null
     */
    public static function getTableName()
    {
        return 'b_iblock_element_property';
    }

    /**
     * @return array
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new ORM\Fields\IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new ORM\Fields\IntegerField('IBLOCK_PROPERTY_ID'),
            new ORM\Fields\Relations\Reference(
                'IBLOCK_PROPERTY',
                \Bitrix\Iblock\PropertyTable::class,
                [
                    '=this.IBLOCK_PROPERTY_ID' => 'ref.ID'
                ]
            ),
            new ORM\Fields\IntegerField('IBLOCK_ELEMENT_ID'),
            new ORM\Fields\Relations\Reference(
                'IBLOCK_PROPERTY',
                \Bitrix\Iblock\ElementTable::class,
                [
                    '=this.IBLOCK_ELEMENT_ID' => 'ref.ID'
                ]
            ),
            new ORM\Fields\StringField('VALUE'),
            new ORM\Fields\StringField('VALUE_TYPE'),
            new ORM\Fields\StringField('VALUE_ENUM'),
            new ORM\Fields\IntegerField('VALUE_NUM'),
            new ORM\Fields\StringField('DESCRIPTION'),
        ];
    }
}