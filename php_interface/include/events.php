<?php
IncludeModuleLangFile(__FILE__);
AddEventHandler("main", "OnBeforeEventAdd", array("Ex2", "Ex2_51"));
AddEventHandler("main", "OnBeforeProlog", array("Ex2", "Ex2_94"));

class Ex2
{

    function Ex2_51(&$event, &$lid, &$arFields)
    {
        if ($event == "FEEDBACK_FORM") {
            global $USER;
            // Если пользователь авторизован
            if ($USER->isAuthorized()) {
                $arFields["AUTHOR"] = GetMessage("EX2_51_AUTH_USER", array(
                    "#ID#" => $USER->GetID(),
                    "#LOGIN#" => $USER->GetLogin(),
                    "#NAME#" =>  $USER->GetFullName(),
                    "#NAME_FORM#" => $arFields["AUTHOR"]
                    )
                );
            } else {
                $arFields["AUTHOR"] = GetMessage("EX2_51_NO_AUTH_USER", array(
                        "#NAME_FORM#" => $arFields["AUTHOR"]
                    )
                );
            }

            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => GetMessage("EX2_51_REPLACEMENT"),
                "MODULE_ID" => "main",
                "ITEM_ID" => $event,
                "DESCRIPTION" => GetMessage("EX2_51_REPLACEMENT").'-'.$arFields["AUTHOR"],
            ));
        }
    }
    function Ex2_94()
    {
        global $APPLICATION;
        $curPage = $APPLICATION->GetCurDir();

        if (\Bitrix\Main\Loader::includeModule("iblock")) {
            $arFilter = array(
                "IBLOCK_ID" => IBLOCK_META,
                "NAME" => $curPage
            );
            $arSelect = array(
                "IBLOCK_ID",
                "ID",
                "PROPERTY_title",
                "PROPERTY_description",
            );

            $ob = CIBlockElement::GetList(
                array(),
                $arFilter,
                false,
                false,
                $arSelect
            );
            if ($arRes = $ob->Fetch()) {

                $APPLICATION->SetPageProperty('title', $arRes["PROPERTY_TITLE_VALUE"]);
                $APPLICATION->SetPageProperty('description', $arRes["PROPERTY_DESCRIPTION_VALUE"]);
            }
        }
    }
}