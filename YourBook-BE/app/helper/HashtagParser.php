<?php

namespace App\helper;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

class HashtagParser implements InlineParserInterface
{

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::string('#');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();

        // The # symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);

        if ($previousChar !== null && $previousChar !== ' ') {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // Save the cursor state in case we need to rewind and bail
        $previousState = $cursor->saveState();

        // Advance past the # symbol to keep parsing simpler
        $cursor->advance();

        // Parse the tag

        $tag = $cursor->match('/^[A-Za-z0-9_]{1,200}(?!\w)/');

        if (empty($tag)) {
            // Regex failed to match; this isn't a valid Twitter handle
            $cursor->restoreState($previousState);
            return false;
        }

        app('tagqueue')->addTag($tag);

        $tagUrl = '/tag/' . $tag;

        $inlineContext->getContainer()->appendChild(new Link($tagUrl, '#' . $tag));

        return true;
    }
}
