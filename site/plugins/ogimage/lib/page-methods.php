<?php

namespace mauricerenck\OgImage;

return [
    'hasOgImage' => function () {
        $ogImageField = option('mauricerenck.ogimage.field', 'ogImage');
        return (!is_null($this->{$ogImageField}()) && $this->{$ogImageField}()->isNotEmpty());
    },
    'hasGeneratedOgImage' => function (string $language) {
        $filename = 'generated-og-image.' . $language . '.png';
        $savedOgImage = !is_null($this->image($filename)) && $this->image($filename)->exists();

        return $savedOgImage;
    },
    'getOgImage' => function () {
        $ogImageField = option('mauricerenck.ogimage.field', 'ogImage');
        $imageWidth = option('mauricerenck.ogimage.width', 1600);
        $imageHeight = option('mauricerenck.ogimage.height', 900);

        return (!is_null($this->{$ogImageField}()) && $this->{$ogImageField}()->isNotEmpty())
            ? $this->{$ogImageField}()->toFile()->crop($imageWidth, $imageHeight)
            : null;
    },
    'createOgImage' => function (string $language) {
        $imageWidth = option('mauricerenck.ogimage.width', 1600);
        $imageHeight = option('mauricerenck.ogimage.height', 900);

        $font = option('mauricerenck.ogimage.font.path', null);
        $fontColor = option('mauricerenck.ogimage.font.color', [0, 0, 0]);
        $fontSize = option('mauricerenck.ogimage.font.size', 80);
        $fontLineHeight = option('mauricerenck.ogimage.font.lineheight', 2);

        $templateImagePath = option('mauricerenck.ogimage.image.template', __DIR__ . '/../assets/template.png');

        $heroImageField = option('mauricerenck.ogimage.heroImage.field', 'hero');
        $heroImageCropSize = option('mauricerenck.ogimage.heroImage.cropsize', [600, 600]);
        $heroImagePosition = option('mauricerenck.ogimage.heroImage.position', [0, 0]);
        $heroImageFallbackColor = option('mauricerenck.ogimage.heroImage.fallbackColor', [255, 123, 123]);
        $heroImageFallbackImage = option('mauricerenck.ogimage.heroImage.fallbackImage', null);

        $titleField = option('mauricerenck.ogimage.title.field', 'title');
        $titlePosition = option('mauricerenck.ogimage.title.position', [0, 0]);
        $titleCharactersPerLine = option('mauricerenck.ogimage.title.charactersPerLine', 20);

        $hedField = option('mauricerenck.ogimage.hed.field', 'hed');

        $dekEnabled = option('mauricerenck.ogimage.dek.enabled', true);
        $dekField = option('mauricerenck.ogimage.dek.field', 'dek');
        $dekPosition = option('mauricerenck.ogimage.dek.position', [0, 100]);
        $dekCharactersPerLine = option('mauricerenck.ogimage.dek.charactersPerLine', 40);
        $dekFontSize = option('mauricerenck.ogimage.dek.size', (int) round($fontSize * 0.55));
        $dekFont = option('mauricerenck.ogimage.dek.font', $font);
        $dekFontColor = option('mauricerenck.ogimage.dek.color', $fontColor);
        $dekLineHeight = option('mauricerenck.ogimage.dek.lineheight', $fontLineHeight);

        if (is_null($font)) {
            return;
        }

        if ($language == 'default') {
            if (!is_null($this->{$hedField}()) && $this->{$hedField}()->isNotEmpty()) {
                $title = $this->{$hedField}();
            } elseif ($this->{$titleField}()->isNotEmpty()) {
                $title = $this->{$titleField}();
            } else {
                $title = $this->title();
            }
        } else {
            $translation = $this->translation($language);
            $content = $translation->content();
            if (!empty($content[$hedField])) {
                $title = $content[$hedField];
            } elseif (!empty($content[$titleField])) {
                $title = $content[$titleField];
            } else {
                $title = $content['title'];
            }
        }

        $dek = null;
        if ($dekEnabled) {
            if ($language == 'default') {
                $dek = $this->{$dekField}()->isNotEmpty() ? (string) $this->{$dekField}() : null;
            } else {
                $translation = $this->translation($language);
                $content = $translation->content();
                $dek = !empty($content[$dekField]) ? $content[$dekField] : null;
            }
        }

        $canvas = imagecreatetruecolor($imageWidth, $imageHeight);
        $textColor = imagecolorallocate($canvas, $fontColor[0], $fontColor[1], $fontColor[2]);
        $templateImage = imagecreatefrompng($templateImagePath);

        $backgroundFile = !is_null($this->{$heroImageField}()) && $this->{$heroImageField}()->isNotEmpty()
            ? $this->{$heroImageField}()->toFile()->crop($heroImageCropSize[0], $heroImageCropSize[1])
            : null;

        if (!is_null($backgroundFile)) {
            $filename = $backgroundFile->root();

            switch ($backgroundFile->mime()) {
                case 'image/jpeg':
                    $background = imagecreatefromjpeg($filename);
                    break;
                case 'image/png':
                    $background = imagecreatefrompng($filename);
                    break;
                case 'image/webp':
                    $background = imagecreatefromwebp($filename);
                    break;
                default:
                    $background = imagecreatefrompng($filename);
                    break;
            }

            imagecopyresampled(
                $canvas,
                $background,
                $heroImagePosition[0],
                $heroImagePosition[1],
                0,
                0,
                imagesx($background),
                imagesy($background),
                imagesx($background),
                imagesy($background)
            );
        } else if (!is_null($heroImageFallbackImage)) {
            $background = imagecreatefrompng($heroImageFallbackImage);

            imagecopyresampled(
                $canvas,
                $background,
                0,
                0,
                0,
                0,
                imagesx($background),
                imagesy($background),
                imagesx($background),
                imagesy($background)
            );
        } else {
            $color = imagecolorallocate($canvas, $heroImageFallbackColor[0], $heroImageFallbackColor[1], $heroImageFallbackColor[2]);
            imagefill($canvas, 0, 0, $color);
        }

        imagecopyresampled(
            $canvas,
            $templateImage,
            0,
            0,
            0,
            0,
            imagesx($templateImage),
            imagesy($templateImage),
            imagesx($templateImage),
            imagesy($templateImage)
        );

        // SET TEXT
        $avoidOrphans = function (array $lines): array {
            if (count($lines) >= 2) {
                $last = trim($lines[count($lines) - 1]);
                if (substr_count($last, ' ') === 0) {
                    $prev = $lines[count($lines) - 2];
                    $lastSpace = strrpos($prev, ' ');
                    if ($lastSpace !== false) {
                        $lines[count($lines) - 2] = substr($prev, 0, $lastSpace);
                        $lines[count($lines) - 1] = substr($prev, $lastSpace + 1) . ' ' . $last;
                    }
                }
            }
            return $lines;
        };

        $imageTitle = wordwrap($title, $titleCharactersPerLine, "\n", true);
        $lines = $avoidOrphans(explode("\n", $imageTitle));

        $y = $fontSize;
        $titleEndY = $titlePosition[1];
        foreach ($lines as $line) {
            $titleEndY = (int) ($titlePosition[1] + $y);
            imagettftext($canvas, $fontSize, 0, (int) $titlePosition[0], $titleEndY, $textColor, $font, $line);
            $y += $fontSize * $fontLineHeight;
        }

        // SET DEK TEXT
        if ($dekEnabled && !is_null($dek)) {
            $dekTextColor = imagecolorallocate($canvas, $dekFontColor[0], $dekFontColor[1], $dekFontColor[2]);
            $imageDek = wordwrap($dek, $dekCharactersPerLine, "\n", true);
            $dekLines = $avoidOrphans(explode("\n", $imageDek));

            $dekGap = $dekPosition[1];
            $dekY = $dekFontSize;
            foreach ($dekLines as $dekLine) {
                imagettftext($canvas, $dekFontSize, 0, (int) $dekPosition[0], (int) ($titleEndY + $dekGap + $dekY), $dekTextColor, $dekFont, $dekLine);
                $dekY += $dekFontSize * $dekLineHeight;
            }
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'png') . '.png';
        imagepng($canvas, $tempFile);

        $filename = 'generated-og-image.' . $language . '.png';

        kirby()->impersonate('kirby');
        if (!is_null($this->file($filename)) && $this->file($filename)->exist()) {
            $this->file($filename)->delete($filename);
        }

        $this->createFile([
            'filename' => $filename,
            'template' => 'image',
            'source' => $tempFile,
            'parent' => $this,
            'content' => [
                'alt' => 'og-image'
            ],
        ]);

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    },
];
