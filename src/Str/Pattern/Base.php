<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.3
 * @package Foundation
 */

namespace FireHub\Foundation\Str\Pattern;

use FireHub\Core\Type\Str;
use FireHub\Foundation\Str\Boundary\Patternable;
use FireHub\Foundation\Str\Exception\InvalidPatternException;
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Provides the common foundation for pattern-based string operations
 *
 * Defines the shared base implementation for components that perform regular expression operations on immutable
 * string value objects.
 *
 * This abstraction centralizes access to the underlying string instance, allowing specialized implementations such
 * as matching, replacing, and splitting to share common behavior while remaining focused on their individual
 * responsibilities.
 * @since 1.0.0
 *
 * @template TPatternable of \FireHub\Foundation\Str\Boundary\Patternable
 *
 * @method mixed letters () Letters
 * @method mixed notLetters () Not-letters
 * @method mixed modifierLetters () Modifier letters
 * @method mixed notModifierLetters () Not-modifier letters
 * @method mixed otherLetters () Other letters
 * @method mixed notOtherLetters () Not-other letters
 * @method mixed lower () Lower case letters
 * @method mixed upper () Upper case letters
 * @method mixed titleCased () Title-cased letters
 * @method mixed notTitleCased () Not-title-cased letters
 * @method mixed numbers () Numbers
 * @method mixed notNumbers () Not-numbers
 * @method mixed letterNumbers () Letter numbers
 * @method mixed notLetterNumbers () Not-letter numbers
 * @method mixed otherNumbers () Other numbers
 * @method mixed notOtherNumbers () Not-other numbers
 * @method mixed digits () Digits
 * @method mixed notDigits () Not-digits
 * @method mixed lettersAndDigits () Letters and digits
 * @method mixed notLettersNorDigits () Not-letters nor digits
 * @method mixed ascii () Character codes 0-127
 * @method mixed notAscii () Character codes not from 0-127
 * @method mixed blanks () Space or tab only
 * @method mixed notBlanks () Not space nor tab only
 * @method mixed control () Control characters
 * @method mixed notControl () Not-control characters
 * @method mixed whitespaces () Whitespaces
 * @method mixed notWhitespaces () Not-whitespaces
 * @method mixed printable () Printing characters, including space
 * @method mixed notPrintable () Not-printing characters, including space
 * @method mixed graphical () Printing characters, excluding space
 * @method mixed notGraphical () Not-printing characters, excluding space
 * @method mixed wordCharacters () Underscore or any character that is a letter or digit
 * @method mixed notWordCharacters () Not-underscore or any character that is not a letter nor digit
 * @method mixed hexadecimals () Hexadecimals
 * @method mixed notHexadecimals () Not-hexadecimals
 * @method mixed verticalWhitespaces () Vertical whitespaces
 * @method mixed notVerticalWhitespaces () Not-vertical whitespaces
 * @method mixed horizontalWhitespaces () Horizontal whitespaces
 * @method mixed notHorizontalWhitespaces () Not-horizontal whitespaces
 * @method mixed wordBoundaries () Word boundaries
 * @method mixed notWordBoundaries () Not-word boundaries
 * @method mixed format () Format
 * @method mixed notFormat () Not-format
 * @method mixed punctuation () Punctuation
 * @method mixed notPunctuation () Not punctuation
 * @method mixed connectorPunctuation () Connector punctuation
 * @method mixed notConnectorPunctuation () Not-connector punctuation
 * @method mixed dashPunctuation () Dash punctuation
 * @method mixed notDashPunctuation () Not-dash punctuation
 * @method mixed closePunctuation () Close punctuation
 * @method mixed notClosePunctuation () Not-close punctuation
 * @method mixed finalPunctuation () Final punctuation
 * @method mixed notFinalPunctuation () Not-final punctuation
 * @method mixed initialPunctuation () Initial punctuation
 * @method mixed notInitialPunctuation () Not-initial punctuation
 * @method mixed otherPunctuation () Other punctuation
 * @method mixed notOtherPunctuation () Not-other punctuation
 * @method mixed openPunctuation () Open punctuation
 * @method mixed notOpenPunctuation () Not-open punctuation
 * @method mixed symbol () Symbol
 * @method mixed notSymbol () Not-symbol
 * @method mixed currency () Currency symbol
 * @method mixed notCurrency () Not-currency symbol
 * @method mixed modifier () Modifier symbol
 * @method mixed notModifier () Not-modifier symbol
 * @method mixed mathematical () Mathematical symbol
 * @method mixed notMathematical () Not-mathematical symbol
 * @method mixed otherSymbol () An other symbol
 * @method mixed notOtherSymbol () Not another symbol
 * @method mixed unassigned () Unassigned – characters with code points greater than 0x10FFFF
 * @method mixed assigned () Assigned – characters with code points smaller than 0x10FFFF
 * @method mixed notNewLine () not-new line
 * @method mixed mark () Mark
 * @method mixed notMark () Non-mark
 * @method mixed spacingMark () Spacing mark
 * @method mixed nonSpacingMark () Non-spacing mark
 * @method mixed notSpacingMark () Not-spacing mark
 * @method mixed notNonSpacingMark () Not-non-spacing mark
 * @method mixed enclosingMark () Enclosing mark
 * @method mixed notEnclosingMark () Not-enclosing mark
 * @method mixed separator () Separator
 * @method mixed notSeparator () Not-separator mark
 * @method mixed lineSeparator () Line separator
 * @method mixed notLineSeparator () Not-line separator
 * @method mixed paragraphSeparator () Paragraph separator
 * @method mixed notParagraphSeparator () Not-paragraph separator
 * @method mixed spaceSeparator () Space separator
 * @method mixed notSpaceSeparator () Not-space separator
 */
abstract readonly class Base {

    /**
     * ### The default regex flags to apply to pattern operations
     * @since 1.0.0
     *
     * @var \FireHub\Runtime\Type\Str\RegexFlag[]
     */
    protected const array DEFAULT_FLAGS = [
        RegexFlag::MULTIBYTE
    ];

    /**
     * ### The predefined patterns
     * @since 1.0.0
     *
     * @var array<non-empty-string, non-empty-string>
     */
    protected const array PATTERNS = [
        'letters' => '[[:alpha:]]',
        'notLetters' => '[[:^alpha:]]',
        'digits' => '[[:digit:]]',
        'notDigits' => '[[:^digit:]]',
        'lettersAndDigits' => '[[:alnum:]]',
        'notLettersNorDigits' => '[[:^alnum:]]',
        'ascii' => '[[:ascii:]]',
        'notAscii' => '[[:^ascii:]]',
        'blanks' => '[[:blank:]]',
        'notBlanks' => '[[:^blank:]]',
        'control' => '[[:cntrl:]]',
        'notControl' => '[[:^cntrl:]]',
        'lower' => '[[:lower:]]',
        'upper' => '[[:upper:]]',
        'whitespaces' => '[[:space:]]',
        'notWhitespaces' => '[[:^space:]]',
        'printable' => '[[:print:]]',
        'notPrintable' => '[[:^print:]]',
        'graphical' => '[[:graph:]]',
        'notGraphical' => '[[:^graph:]]',
        'wordCharacters' => '[[:word:]]',
        'notWordCharacters' => '[[:^word:]]',
        'hexadecimals' => '[[:xdigit:]]',
        'notHexadecimals' => '[[:^xdigit:]]',
        'verticalWhitespaces' => '\v',
        'notVerticalWhitespaces' => '\V',
        'horizontalWhitespaces' => '\h',
        'notHorizontalWhitespaces' => '\H',
        'wordBoundaries' => '\b',
        'notWordBoundaries' => '\B',
        'notNewLine' => '\N',
        'format' => '\p{Cf}',
        'notFormat' => '\P{Cf}',
        'modifierLetters' => '\p{Lm}',
        'notModifierLetters' => '\P{Lm}',
        'otherLetters' => '\p{Lo}',
        'notOtherLetters' => '\P{Lo}',
        'titleCased' => '\p{Lt}',
        'notTitleCased' => '\P{Lt}',
        'numbers' => '\p{N}',
        'notNumbers' => '\P{N}',
        'letterNumbers' => '\p{Nl}',
        'notLetterNumbers' => '\P{Nl}',
        'otherNumbers' => '\p{No}',
        'notOtherNumbers' => '\P{No}',
        'punctuation' => '\p{P}',
        'notPunctuation' => '\P{P}',
        'connectorPunctuation' => '\p{Pc}',
        'notConnectorPunctuation' => '\P{Pc}',
        'dashPunctuation' => '\p{Pd}',
        'notDashPunctuation' => '\P{Pd}',
        'closePunctuation' => '\p{Pe}',
        'notClosePunctuation' => '\P{Pe}',
        'finalPunctuation' => '\p{Pf}',
        'notFinalPunctuation' => '\P{Pf}',
        'initialPunctuation' => '\p{Pi}',
        'notInitialPunctuation' => '\P{Pi}',
        'otherPunctuation' => '\p{Po}',
        'notOtherPunctuation' => '\P{Po}',
        'openPunctuation' => '\p{Ps}',
        'notOpenPunctuation' => '\P{Ps}',
        'symbol' => '\p{S}',
        'notSymbol' => '\P{S}',
        'currency' => '\p{Sc}',
        'notCurrency' => '\P{Sc}',
        'modifier' => '\p{Sk}',
        'notModifier' => '\P{Sk}',
        'mathematical' => '\p{Sm}',
        'notMathematical' => '\P{Sm}',
        'otherSymbol' => '\p{So}',
        'notOtherSymbol' => '\P{So}',
        'assigned' => '\p{Cn}',
        'unassigned' => '\P{Cn}',
        'mark' => '\p{M}',
        'notMark' => '\P{M}',
        'spacingMark' => '\p{Mc}',
        'nonSpacingMark' => '\p{Mn}',
        'notSpacingMark' => '\P{Mc}',
        'notNonSpacingMark' => '\P{Mn}',
        'enclosingMark' => '\p{Me}',
        'notEnclosingMark' => '\P{Me}',
        'separator' => '\p{Z}',
        'notSeparator' => '\P{Z}',
        'lineSeparator' => '\p{Zl}',
        'notLineSeparator' => '\P{Zl}',
        'paragraphSeparator' => '\p{Zp}',
        'notParagraphSeparator' => '\P{Zp}',
        'spaceSeparator' => '\p{Zs}',
        'notSpaceSeparator' => '\P{Zs}',
    ];

    /**
     * ### The delimiter to use for pattern matching
     * @since 1.0.0
     *
     * @var \FireHub\Runtime\Type\Str\RegexDelimiter
     */
    protected RegexDelimiter $delimiter;

    /**
     * ### The regex flags to apply to the pattern operation
     * @since 1.0.0
     *
     * @var \FireHub\Runtime\Type\Str\RegexFlag[]
     */
    protected array $flags;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To remove duplicate flags.
     * @uses \FireHub\Runtime\Arr\Transform::unique() To remove duplicate flags.
     * @uses \FireHub\Foundation\Str\Pattern\Base::DEFAULT_FLAGS To set the default flags.
     *
     * @param \FireHub\Core\Type\Str<string>&TPatternable $str <p>
     * The string value to operate on.
     * </p>
     * @param \FireHub\Runtime\Type\Str\RegexDelimiter $delimiter [optional] <p>
     * The delimiter to use for pattern matching.
     * </p>
     * @param \FireHub\Runtime\Type\Str\RegexFlag ...$flags [optional] <p>
     * The regex flags to apply to the pattern operation.
     * </p>
     *
     * @return void
     *
     * @note RegexFlag::MULTIBYTE is always applied by default to ensure proper handling of multibyte characters in
     * pattern operations.
     */
    public function __construct (
        protected Str&Patternable $str,
        RegexDelimiter $delimiter = RegexDelimiter::SLASH,
        RegexFlag ...$flags
    ) {

        $this->delimiter = $delimiter;

        $this->flags = Runtime\Arr\Access::values(
            Runtime\Arr\Transform::unique([
                ...self::DEFAULT_FLAGS,
                ...$flags
            ])
        );

    }

    /**
     * ### Performs the pattern matching operation
     * @since 1.0.0
     *
     * @param string $pattern <p>
     * The pattern to match.
     * </p>
     *
     * @return mixed The result of the pattern matching operation.
     */
    abstract public function custom (string $pattern):mixed;

    /**
     * ### Builds the pattern to use for the operation
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To build the modifiers string.
     * @uses \FireHub\Runtime\Arr\Transform::map() To map the flags to their string representations.
     *
     * @param string $pattern <p>
     * The pattern to build.
     * </p>
     *
     * @return non-empty-string The built pattern.
     */
    protected function patternBuilder (string $pattern):string {

        $modifiers = Runtime\Str\SB\Delimiter::implode(
            Runtime\Arr\Transform::map(
                $this->flags,
                static fn(RegexFlag $flag): string => $flag->value
            )
        );

        return $this->delimiter->value . $pattern . $this->delimiter->value . $modifiers;

    }

    /**
     * ### Magic method to handle custom pattern matching operations
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Pattern\Base::custom() To perform the custom pattern matching operation.
     *
     * @param string $method <p>
     * The name of the method being called.
     * </p>
     * @param array<array-key, mixed> $arguments <p>
     * The arguments passed to the method.
     * </p>
     *
     * @throws \FireHub\Foundation\Str\Exception\InvalidPatternException If the method doesn't exist.
     *
     * @return mixed The result of the custom pattern matching operation.
     */
    public function __call (string $method, array $arguments):mixed {

        return isset(self::PATTERNS[$method])
            ? $this->custom(self::PATTERNS[$method])
            : throw new InvalidPatternException("Method $method doesn't exist.");

    }

}