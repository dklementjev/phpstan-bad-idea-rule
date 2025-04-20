<?php

use Dklementjev\Phpstan\BadIdea\Attribute\BadIdea;
class Simple
{
    public function doSomething(): void
    {
        $this->doSomeMessyStuff();

        $this->doSomeSemilegacyStuff(false);

        $this->doSomeSemilegacyStuff(true);

        $this->doApplyDiscount(1);

        $this->doApplyDiscount(7.5);

        $this->doApplyDiscount(-1);
    }

    /**
     * Deprecated my old friend
     */
    #[BadIdea(why: 'Just don\'t do it')]
    public function doSomeMessyStuff(): void
    {
        // Void
    }

    /**
     * Call arguments are passed to expression, position and argument name indices are set
     */
    #[BadIdea(why: 'Use newer approach in new code written', when: 'arguments[0]')]
    public function doSomeSemilegacyStuff(bool $useLegacyApproach): void
    {
        // Void
    }

    /**
     * The attribute is repeatable
     */
    #[BadIdea(why: '<2% ? Just give \'em free coffee', when: 'arguments[\'percentage\']>=0 && arguments[\'percentage\'] < 2')]
    #[BadIdea(why: 'A negative discount. How frustrating', when: 'arguments[\'percentage\']<0')]
    public function doApplyDiscount(float $percentage): void
    {
        // Void
    }
}