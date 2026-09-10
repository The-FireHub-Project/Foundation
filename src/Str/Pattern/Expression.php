<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.4
 * @package Foundation
 */

namespace FireHub\Foundation\Str\Pattern;

use FireHub\Foundation\Str\Exception\InvalidPatternException;

/**
 * ### Defines an abstract class for regex pattern expressions
 *
 * Provides a common abstract class for objects that transform logical pattern expressions into regular expression
 * fragments.
 *
 * Expressions encapsulate reusable regex construction logic, allowing pattern operations such as matching,
 * replacing, and splitting to compose complex expressions without directly handling regular expression syntax.
 * @since 1.0.0
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
abstract readonly class Expression {

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
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Str\Pattern\Base<\FireHub\Foundation\Str\Boundary\Patternable> $base <p>
     * The base pattern object that this expression is associated with.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected Base $base
    ) {}

    /**
     * ### Applies the expression to a string
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Pattern\Base::custom() As a wrapper around the base pattern object's custom method.
     * @uses \FireHub\Foundation\Str\Pattern\Expression::regex() To generate the regular expression fragment.
     *
     * @param string $pattern <p>
     * The string to apply the expression to.
     * </p>
     *
     * @return mixed The result of applying the expression to the string.
     */
    final public function custom (string $pattern):mixed {

        return $this->base->custom($this->regex($pattern));

    }

    /**
     * ### Converts the expression into a regular expression fragment
     * @since 1.0.0
     *
     * @param string $pattern <p>
     * The pattern value to transform into a regular expression fragment.
     * </p>
     *
     * @return string The generated regular expression fragment.
     */
    abstract protected function regex (string $pattern):string;

    /**
     * ### Magic method to handle custom pattern matching operations
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Pattern\Expression::custom() To perform the custom pattern matching operation.
     * @uses \FireHub\Foundation\Str\Pattern\Expression::PATTERNS To get predefined pattern.
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
            ? $this->custom(self::PATTERNS[$method].'+')
            : throw new InvalidPatternException("Method $method doesn't exist.");

    }

}