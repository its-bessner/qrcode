<?php

namespace ItsBessner\Qrcode\Lib;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class Qrcode
{
    const NAME = "Harrie 1.0.6";

    public static function getConfig()
    {
        return ["qr_content" => [
            "label" => ["Inhalt", "Zu kodierender Inhalt. Z.B. URL"],
            "inputType" => "text",
        ],
            "qr_text" => [
                "label" => ["Bildunterschrift", "Text unter dem QR-Code (optional)"],
                "inputType" => "text",
                "eval" => [
                ]
            ],
            "qr_foreground" => [
                "label" => ["Vordergrundfarbe", "Schrift und Code"],
                "inputType" => "text",
                "eval" => [
                    "colorpicker" => true,
                    "default" => "ffffff"
                ]
            ],
            "qr_background" => [
                "label" => ["Hintergrundfarbe", "Hintergrundfarbe des QR-Codes. Vorzugsweise mit Transparenz kombinieren."],
                "inputType" => "text",
                "eval" => [
                    "colorpicker" => true,
                    "isHexColor" => true,
                ]
            ],
            "qr_background_opacity" => [
                "label" => ["Hintergrunddeckkraft", "Deckkraft (opacity) des umgebenden Rahmens. 0 (durchsichtig) - 100 (Vollfarbe)"],
                "inputType" => "select",
                "options" => [0 => "0%", 25 => "25%", 50 => "50%", 75 => "75%", 100 => "100%", -1 => "(Ohne)"],

            ],

            'qr_size' => [
                'label' => ['Größe', ''],
                'inputType' => 'select',
                'options' => [180 => "Groß", 120 => "Medium", 80 => "Klein"]
            ],
            'qr_margin' => [
                'label' => ['Rahmen', ''],
                'inputType' => 'select',
                'options' => [15 => "Groß", 10 => "Medium", 5 => "Klein", 0 =>  "(Ohne)"]
            ],

            "qr_css_class" => [
                'label' => ["CSS-Klasse", "(Wird von nachfolgenden Einstellungen ggf. überschrieben!)"],
                "inputType" => "text",
                "eval" => [
                ]

            ],
            'qr_css_position' => [
                'label' => ["Darstellungsoption", '(CSS-Attribut "position")'],
                'inputType' => 'select',
                'options' => ["absolute" => "absolute", "relative" => "relative", "fixed" => "fixed", 'inherit' => "(Ohne)"]
            ],
            'qr_position' => [
                "label" => ["Position", ''],
                "inputType" => "select",
                "options" => [
                    "top-left" => "Oben Links", "top-center" => "Oben Mitte", "top-right" => "Oben Rechts",
                    "bottom-left" => "Unten Links", "bottom-center" => "Unten Mitte", "bottom-right" => "Unten Rechts",
                    "none" => "(Ohne)"
                ],
            ],
            'qr_zindex' => [
                'label' => ["Z-Index"],
                'inputType' => "text",
                'eval' => [
                        'type' => "number"
                ]
            ],
        ];
    }

    public static function generate(&$config)
    {


        // No content, no qr code!
        if (empty(trim($config->qr_content))) {
            return;
        }

//        var_dump($config->qr_content);
        $builder = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($config->qr_size)
            ->margin($config->qr_margin)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
//            ->logoPath(__DIR__ . '/bwh-logo.png')
//            ->logoResizeToWidth(40)
//            ->logoPunchoutBackground(true)
            ->validateResult(false)
            ->data($config->qr_content);

        list($r, $g, $b) = str_split($config->qr_foreground, 2);
        list($br, $bg, $bb) = str_split($config->qr_background, 2);

        $config->qr_background_opacity = (int)$config->qr_background_opacity;
        $opacity = 127 - 1.27 * max(0, min(100, $config->qr_background_opacity));
        $foreground = new Color(hexdec($r), hexdec($g), hexdec($b), 0);
        $background = new Color(hexdec($br), hexdec($bg), hexdec($bb), $opacity);

        if ($config->text) {
            $builder->labelText($config->qr_text)
                ->labelTextColor($foreground)
                ->labelFont(new NotoSans(12))
                ->labelAlignment(new LabelAlignmentCenter());
        }
        $builder
            ->backgroundColor($background)
            ->foregroundColor($foreground);


        $result = $builder->build();




        $style = [];
        switch($config->qr_position) {
            case "top-left":
                $style[] = "top: 1rem; left: 1rem;";
                break;
            case "top-center":
                $style[] = "top: 1rem; left: 50%; transform: translateX(-50%)";
                break;
            case "top-right":
                $style[] = "top: 1rem; right: 1rem";
                break;
            case "bottom-left":
                $style[] = "bottom: 1rem; left: 1rem;";
                break;
            case "bottom-center":
                $style[] = "bottom: 1rem; left: 50%; transform: translateX(-50%)";
                break;
            case "bottom-right":
                $style[] = "bottom: 1rem; right: 1rem";
                break;

        }
        if($config->qr_css_position != "inherit") {
            $style[] = "position: {$config->qr_css_position}";
        }
        if($config->qr_zindex) {
            $style[] = "z-index: {$config->qr_zindex}";
        }

        ?>


        <img style="<?= implode("; ", $style) ?>" src="<?= $result->getDataUri(); ?>"/>


        <?php

    }
}