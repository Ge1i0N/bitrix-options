<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Traits;

use Gelion\BitrixOptions\Enum\TagColor;

trait WithTag
{
    public ?TagColor $tagColor = null;

    public ?string $tag = '';

    public function setTag($tag, TagColor $tagColor = TagColor::DEFAULT): static
    {
        $this->tag = (string) $tag;
        $this->tagColor = $tagColor;

        return $this;
    }

    protected function getTagView(): string
    {
        return mb_strlen(trim($this->tag)) > 0
            ? sprintf('<div class="ui-ctl-tag %s">%s</div>',
                $this->tagColor?->toString() ?? '',
                $this->tag,
            )
            : '';
    }
}
