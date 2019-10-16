<?php

namespace Illuminate\Tests\Support;

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class SupportStrTest extends TestCase
{
    public function testStringCanBeLimitedByWords()
    {
        $this->assertSame('Taylor...', Str::words('Taylor Otwell', 1));
        $this->assertSame('Taylor___', Str::words('Taylor Otwell', 1, '___'));
        $this->assertSame('Taylor Otwell', Str::words('Taylor Otwell', 3));
    }

    public function testStringTrimmedOnlyWhereNecessary()
    {
        $this->assertSame(' Taylor Otwell ', Str::words(' Taylor Otwell ', 3));
        $this->assertSame(' Taylor...', Str::words(' Taylor Otwell ', 1));
    }

    public function testStringTitle()
    {
        $this->assertSame('Jefferson Costella', Str::title('jefferson costella'));
        $this->assertSame('Jefferson Costella', Str::title('jefFErson coSTella'));
    }

    public function testStringWithoutWordsDoesntProduceError()
    {
        $nbsp = chr(0xC2).chr(0xA0);
        $this->assertSame(' ', Str::words(' '));
        $this->assertEquals($nbsp, Str::words($nbsp));
    }

    public function testStringAscii()
    {
        $this->assertSame('@', Str::ascii('@'));
        $this->assertSame('u', Str::ascii('ü'));
    }

    public function testStringAsciiWithSpecificLocale()
    {
        $this->assertSame('h H sht Sht a A ia yo', Str::ascii('х Х щ Щ ъ Ъ иа йо', 'bg'));
        $this->assertSame('ae oe ue Ae Oe Ue', Str::ascii('ä ö ü Ä Ö Ü', 'de'));
    }

    public function testStartsWith()
    {
        $this->assertTrue(Str::startsWith('jason', 'jas'));
        $this->assertTrue(Str::startsWith('jason', 'jason'));
        $this->assertTrue(Str::startsWith('jason', ['jas']));
        $this->assertTrue(Str::startsWith('jason', ['day', 'jas']));
        $this->assertFalse(Str::startsWith('jason', 'day'));
        $this->assertFalse(Str::startsWith('jason', ['day']));
        $this->assertFalse(Str::startsWith('jason', ''));
        $this->assertFalse(Str::startsWith('7', ' 7'));
        $this->assertTrue(Str::startsWith('7a', '7'));
        $this->assertTrue(Str::startsWith('7a', 7));
        $this->assertTrue(Str::startsWith('7.12a', 7.12));
        $this->assertFalse(Str::startsWith('7.12a', 7.13));
        $this->assertTrue(Str::startsWith(7.123, '7'));
        $this->assertTrue(Str::startsWith(7.123, '7.12'));
        $this->assertFalse(Str::startsWith(7.123, '7.13'));
        // Test for multibyte string support
        $this->assertTrue(Str::startsWith('Jönköping', 'Jö'));
        $this->assertTrue(Str::startsWith('Malmö', 'Malmö'));
        $this->assertFalse(Str::startsWith('Jönköping', 'Jonko'));
        $this->assertFalse(Str::startsWith('Malmö', 'Malmo'));
    }

    public function testEndsWith()
    {
        $this->assertTrue(Str::endsWith('jason', 'on'));
        $this->assertTrue(Str::endsWith('jason', 'jason'));
        $this->assertTrue(Str::endsWith('jason', ['on']));
        $this->assertTrue(Str::endsWith('jason', ['no', 'on']));
        $this->assertFalse(Str::endsWith('jason', 'no'));
        $this->assertFalse(Str::endsWith('jason', ['no']));
        $this->assertFalse(Str::endsWith('jason', ''));
        $this->assertFalse(Str::endsWith('7', ' 7'));
        $this->assertTrue(Str::endsWith('a7', '7'));
        $this->assertTrue(Str::endsWith('a7', 7));
        $this->assertTrue(Str::endsWith('a7.12', 7.12));
        $this->assertFalse(Str::endsWith('a7.12', 7.13));
        $this->assertTrue(Str::endsWith(0.27, '7'));
        $this->assertTrue(Str::endsWith(0.27, '0.27'));
        $this->assertFalse(Str::endsWith(0.27, '8'));
        // Test for multibyte string support
        $this->assertTrue(Str::endsWith('Jönköping', 'öping'));
        $this->assertTrue(Str::endsWith('Malmö', 'mö'));
        $this->assertFalse(Str::endsWith('Jönköping', 'oping'));
        $this->assertFalse(Str::endsWith('Malmö', 'mo'));
    }

    public function testStrBefore()
    {
        $this->assertSame('han', Str::before('hannah', 'nah'));
        $this->assertSame('ha', Str::before('hannah', 'n'));
        $this->assertSame('ééé ', Str::before('ééé hannah', 'han'));
        $this->assertSame('hannah', Str::before('hannah', 'xxxx'));
        $this->assertSame('hannah', Str::before('hannah', ''));
        $this->assertSame('han', Str::before('han0nah', '0'));
        $this->assertSame('han', Str::before('han0nah', 0));
        $this->assertSame('han', Str::before('han2nah', 2));
    }

    public function testStrAfter()
    {
        $this->assertSame('nah', Str::after('hannah', 'han'));
        $this->assertSame('nah', Str::after('hannah', 'n'));
        $this->assertSame('nah', Str::after('ééé hannah', 'han'));
        $this->assertSame('hannah', Str::after('hannah', 'xxxx'));
        $this->assertSame('hannah', Str::after('hannah', ''));
        $this->assertSame('nah', Str::after('han0nah', '0'));
        $this->assertSame('nah', Str::after('han0nah', 0));
        $this->assertSame('nah', Str::after('han2nah', 2));
    }

    public function testStrContains()
    {
        $this->assertTrue(Str::contains('taylor', 'ylo'));
        $this->assertTrue(Str::contains('taylor', 'taylor'));
        $this->assertTrue(Str::contains('taylor', ['ylo']));
        $this->assertTrue(Str::contains('taylor', ['xxx', 'ylo']));
        $this->assertFalse(Str::contains('taylor', 'xxx'));
        $this->assertFalse(Str::contains('taylor', ['xxx']));
        $this->assertFalse(Str::contains('taylor', ''));
    }

    public function testStrContainsAll()
    {
        $this->assertTrue(Str::containsAll('taylor otwell', ['taylor', 'otwell']));
        $this->assertTrue(Str::containsAll('taylor otwell', ['taylor']));
        $this->assertFalse(Str::containsAll('taylor otwell', ['taylor', 'xxx']));
    }

    public function testParseCallback()
    {
        $this->assertEquals(['Class', 'method'], Str::parseCallback('Class@method', 'foo'));
        $this->assertEquals(['Class', 'foo'], Str::parseCallback('Class', 'foo'));
    }

    public function testSlug()
    {
        $this->assertSame('hello-world', Str::slug('hello world'));
        $this->assertSame('hello-world', Str::slug('hello-world'));
        $this->assertSame('hello-world', Str::slug('hello_world'));
        $this->assertSame('hello_world', Str::slug('hello_world', '_'));
        $this->assertSame('user-at-host', Str::slug('user@host'));
        $this->assertSame('سلام-دنیا', Str::slug('سلام دنیا', '-', null));
    }

    public function testStrStart()
    {
        $this->assertSame('/test/string', Str::start('test/string', '/'));
        $this->assertSame('/test/string', Str::start('/test/string', '/'));
        $this->assertSame('/test/string', Str::start('//test/string', '/'));
    }

    public function testFinish()
    {
        $this->assertSame('abbc', Str::finish('ab', 'bc'));
        $this->assertSame('abbc', Str::finish('abbcbc', 'bc'));
        $this->assertSame('abcbbc', Str::finish('abcbbcbc', 'bc'));
    }

    public function testIs()
    {
        $this->assertTrue(Str::is('/', '/'));
        $this->assertFalse(Str::is('/', ' /'));
        $this->assertFalse(Str::is('/', '/a'));
        $this->assertTrue(Str::is('foo/*', 'foo/bar/baz'));

        $this->assertTrue(Str::is('*@*', 'App\Class@method'));
        $this->assertTrue(Str::is('*@*', 'app\Class@'));
        $this->assertTrue(Str::is('*@*', '@method'));

        // is case sensitive
        $this->assertFalse(Str::is('*BAZ*', 'foo/bar/baz'));
        $this->assertFalse(Str::is('*FOO*', 'foo/bar/baz'));
        $this->assertFalse(Str::is('A', 'a'));

        // Accepts array of patterns
        $this->assertTrue(Str::is(['a*', 'b*'], 'a/'));
        $this->assertTrue(Str::is(['a*', 'b*'], 'b/'));
        $this->assertFalse(Str::is(['a*', 'b*'], 'f/'));

        // numeric values and patterns
        $this->assertFalse(Str::is(['a*', 'b*'], 123));
        $this->assertTrue(Str::is(['*2*', 'b*'], 11211));

        $this->assertTrue(Str::is('*/foo', 'blah/baz/foo'));

        $valueObject = new StringableObjectStub('foo/bar/baz');
        $patternObject = new StringableObjectStub('foo/*');

        $this->assertTrue(Str::is('foo/bar/baz', $valueObject));
        $this->assertTrue(Str::is($patternObject, $valueObject));

        //empty patterns
        $this->assertFalse(Str::is([], 'test'));
    }

    public function testKebab()
    {
        // sfrom StudlyCase
        $this->assertSame('laravel-php-framework', Str::kebab('LaravelPhpFramework'));
        // weird whitespaces
        $this->assertSame('laravel-php-framework', Str::kebab("\v  Laravel \n\t   Php \r \n \v     Framework   "));
        $this->assertSame('--laravel-php-framework--', Str::kebab(' _ Laravel  Php Framework    _    '));
        $this->assertSame('/-laravel-php-framework-?', Str::kebab(' / Laravel  Php Framework    ?    '));
        // numbers
        $this->assertSame('laravel6-php-frame-work', Str::kebab('laravel6 php FrameWork'));
        $this->assertSame('laravel6-p-h-p-framework', Str::kebab('laravel6PHPFramework'));
        // from camelCase
        $this->assertSame('laravel-php-framework', Str::kebab('laravelPhpFramework'));
        // from snake_case
        $this->assertSame('laravel-php-framework', Str::kebab('laravel_php_framework'));
        // from Snake_Caps
        $this->assertSame('laravel-php-framework', Str::kebab('Laravel_Php_Framework'));
        // from Url
        $this->assertSame('http://hello-world.org', Str::kebab('http://HelloWorld.org'));
        // from camelCase.nestedMember
        $this->assertSame('i-am.the-nested.member', Str::kebab('iAm.theNested.member'));
        // from StudlyCase.NestedMember
        $this->assertSame('i-am.the-nested.member', Str::kebab('IAm.TheNested.Member'));
        // from snake_case.nested_member
        $this->assertSame('i-am.the-nested.member', Str::kebab('i_am.the_nested.member'));
        // from string with wildcards (I've seen this somewhere in laravel/framework)
        $this->assertSame('i-am.*.last-member', Str::kebab('iAm.*.lastMember'));
        // from string with strange characters
        $this->assertSame('all-the-strange-chars-like:-(!@#$%^&*)-remain-unchanged',
               Str::kebab('All the strangeCharsLike:   (!@#$%^&*) remain unchanged'));
        // from string with multibyte
        $this->assertSame('malmö-jönköping', Str::kebab('Malmö Jönköping'));
        // from string with multibyte caps
        $this->assertSame('łu-kasz.żą-dełko', Str::kebab('ŁuKasz.ŻąDełko'));
        $this->assertSame('łukasz-żądełko', Str::kebab('ŁukaszŻądełko'));
    }

    public function testKebabIden()
    {
        // from StudlyCase
        $this->assertSame('laravel-php-framework', Str::kebabIden('LaravelPhpFramework'));
        // with numbers
        $this->assertSame('laravel6-php-frame-work', Str::kebabIden('laravel6 php FrameWork'));
        $this->assertSame('laravel6-php-framework', Str::kebabIden('laravel6PHPFramework'));
        // with weird whitespaces
        $this->assertSame('laravel-php-framework', Str::kebabIden("\v  Laravel \n\t   Php \r \n \v     Framework   "));
        $this->assertSame('--laravel-php-framework--', Str::kebabIden(' _ Laravel  Php Framework    _    '));
        $this->assertSame('--laravel-php-framework--', Str::kebabIden(' / Laravel  Php Framework    ?    '));
        // from camelCase
        $this->assertSame('laravel-php-framework', Str::kebabIden('laravelPhpFramework'));
        // from snake_case
        $this->assertSame('laravel-php-framework', Str::kebabIden('laravel_php_framework'));
        // from Snake_Caps
        $this->assertSame('laravel-php-framework', Str::kebabIden('Laravel_Php_Framework'));
        // from Url
        $this->assertSame('http---hello-world-org', Str::kebabIden('http://HelloWorld.org'));
        // from camelCase.nestedMember
        $this->assertSame('i-am-the-nested-member', Str::kebabIden('iAm.theNested.member'));
        // from StudlyCase.NestedMember
        $this->assertSame('i-am-the-nested-member', Str::kebabIden('IAm.TheNested.Member'));
        // from snake_case.nester_member
        $this->assertSame('i-am-the-nested-member', Str::kebabIden('i_am.the_nested.member'));
        // from strings with wildcards (I've seen this somewhere in laravel/framework)
        $this->assertSame('i-am---last-member', Str::kebabIden('iAm.*.lastMember'));
        // with strange chars
        $this->assertSame('all-the-strange-chars-like-------------get-replaced',
               Str::kebabIden('All the strangeCharsLike:   (!@#$%^&*) get/replaced'));
        // with multibyte strings
        $this->assertSame('malmö-jönköping', Str::kebabIden('Malmö Jönköping'));
        // with multibyte caps
        $this->assertSame('łu-kasz-żą-dełko', Str::kebabIden('ŁuKasz.ŻąDełko'));
        $this->assertSame('łukasz-żądełko', Str::kebabIden('ŁukaszŻądełko'));
    }

    public function testLower()
    {
        $this->assertSame('foo bar baz', Str::lower('FOO BAR BAZ'));
        $this->assertSame('foo bar baz', Str::lower('fOo Bar bAz'));
    }

    public function testUpper()
    {
        $this->assertSame('FOO BAR BAZ', Str::upper('foo bar baz'));
        $this->assertSame('FOO BAR BAZ', Str::upper('foO bAr BaZ'));
    }

    public function testLimit()
    {
        $this->assertSame('Laravel is...', Str::limit('Laravel is a free, open source PHP web application framework.', 10));
        $this->assertSame('这是一...', Str::limit('这是一段中文', 6));

        $string = 'The PHP framework for web artisans.';
        $this->assertSame('The PHP...', Str::limit($string, 7));
        $this->assertSame('The PHP', Str::limit($string, 7, ''));
        $this->assertSame('The PHP framework for web artisans.', Str::limit($string, 100));

        $nonAsciiString = '这是一段中文';
        $this->assertSame('这是一...', Str::limit($nonAsciiString, 6));
        $this->assertSame('这是一', Str::limit($nonAsciiString, 6, ''));
    }

    public function testLength()
    {
        $this->assertEquals(11, Str::length('foo bar baz'));
        $this->assertEquals(11, Str::length('foo bar baz', 'UTF-8'));
    }

    public function testRandom()
    {
        $this->assertEquals(16, strlen(Str::random()));
        $randomInteger = random_int(1, 100);
        $this->assertEquals($randomInteger, strlen(Str::random($randomInteger)));
        $this->assertIsString(Str::random());
    }

    public function testReplaceArray()
    {
        $this->assertSame('foo/bar/baz', Str::replaceArray('?', ['foo', 'bar', 'baz'], '?/?/?'));
        $this->assertSame('foo/bar/baz/?', Str::replaceArray('?', ['foo', 'bar', 'baz'], '?/?/?/?'));
        $this->assertSame('foo/bar', Str::replaceArray('?', ['foo', 'bar', 'baz'], '?/?'));
        $this->assertSame('?/?/?', Str::replaceArray('x', ['foo', 'bar', 'baz'], '?/?/?'));
        // Ensure recursive replacements are avoided
        $this->assertSame('foo?/bar/baz', Str::replaceArray('?', ['foo?', 'bar', 'baz'], '?/?/?'));
        // Test for associative array support
        $this->assertSame('foo/bar', Str::replaceArray('?', [1 => 'foo', 2 => 'bar'], '?/?'));
        $this->assertSame('foo/bar', Str::replaceArray('?', ['x' => 'foo', 'y' => 'bar'], '?/?'));
    }

    public function testReplaceFirst()
    {
        $this->assertSame('fooqux foobar', Str::replaceFirst('bar', 'qux', 'foobar foobar'));
        $this->assertSame('foo/qux? foo/bar?', Str::replaceFirst('bar?', 'qux?', 'foo/bar? foo/bar?'));
        $this->assertSame('foo foobar', Str::replaceFirst('bar', '', 'foobar foobar'));
        $this->assertSame('foobar foobar', Str::replaceFirst('xxx', 'yyy', 'foobar foobar'));
        $this->assertSame('foobar foobar', Str::replaceFirst('', 'yyy', 'foobar foobar'));
        // Test for multibyte string support
        $this->assertSame('Jxxxnköping Malmö', Str::replaceFirst('ö', 'xxx', 'Jönköping Malmö'));
        $this->assertSame('Jönköping Malmö', Str::replaceFirst('', 'yyy', 'Jönköping Malmö'));
    }

    public function testReplaceLast()
    {
        $this->assertSame('foobar fooqux', Str::replaceLast('bar', 'qux', 'foobar foobar'));
        $this->assertSame('foo/bar? foo/qux?', Str::replaceLast('bar?', 'qux?', 'foo/bar? foo/bar?'));
        $this->assertSame('foobar foo', Str::replaceLast('bar', '', 'foobar foobar'));
        $this->assertSame('foobar foobar', Str::replaceLast('xxx', 'yyy', 'foobar foobar'));
        $this->assertSame('foobar foobar', Str::replaceLast('', 'yyy', 'foobar foobar'));
        // Test for multibyte string support
        $this->assertSame('Malmö Jönkxxxping', Str::replaceLast('ö', 'xxx', 'Malmö Jönköping'));
        $this->assertSame('Malmö Jönköping', Str::replaceLast('', 'yyy', 'Malmö Jönköping'));
    }

    public function testSnake()
    {
        $this->assertSame('laravel_p_h_p_framework', Str::snake('LaravelPHPFramework'));
        $this->assertSame('laravel_php_framework', Str::snake('LaravelPhpFramework'));
        $this->assertSame('laravel php framework', Str::snake('LaravelPhpFramework', ' '));
        $this->assertSame('laravel_php_framework', Str::snake('Laravel Php Framework'));
        $this->assertSame('laravel_php_framework', Str::snake('Laravel    Php      Framework   '));
        // ensure cache keys don't overlap
        $this->assertSame('laravel__php__framework', Str::snake('LaravelPhpFramework', '__'));
        $this->assertSame('laravel_php_framework_', Str::snake('LaravelPhpFramework_', '_'));
        $this->assertSame('laravel_php_framework', Str::snake('laravel php Framework'));
        $this->assertSame('laravel_php_frame_work', Str::snake('laravel php FrameWork'));
        // with weird whitespaces
        $this->assertSame('laravel_php_framework', Str::snake("\v  Laravel \n\t   Php \r \n \v     Framework   "));
        $this->assertSame('__laravel_php_framework__', Str::snake(' - Laravel  Php Framework    -    '));
        $this->assertSame('/_laravel_php_framework_?', Str::snake(' / Laravel  Php Framework    ?    '));
        // with numbers
        $this->assertSame('laravel6_php_frame_work', Str::snake('laravel6 php FrameWork'));
        $this->assertSame('laravel6_p_h_p_framework', Str::snake('laravel6PHPFramework'));
        // from camelCase
        $this->assertSame('laravel_php_framework', Str::snake('laravelPhpFramework'));
        // from kebab case
        $this->assertSame('laravel_php_framework', Str::snake('laravel-php-framework'));
        // from snake caps
        $this->assertSame('laravel_php_framework', Str::snake('Laravel_Php_Framework'));
        // from kebab caps
        $this->assertSame('laravel_php_framework', Str::snake('Laravel-Php-Framework'));
        // from Url
        $this->assertSame('http://hello_world.org', Str::snake('http://HelloWorld.org'));
        // from camelCase.nestedMember
        $this->assertSame('i_am.the_nested.member', Str::snake('iAm.theNested.member'));
        // from StudlyCase.NestedMember
        $this->assertSame('i_am.the_nested.member', Str::snake('IAm.TheNested.Member'));
        // from kebab-case.nested-member
        $this->assertSame('i_am.the_nested.member', Str::snake('i-am.the-nested.member'));
        // from strings with wildcards (I've seen this somewhere in laravel/framework)
        $this->assertSame('i_am.*.last_member', Str::snake('iAm.*.lastMember'));
        // with strange characters
        $this->assertSame('all_the_strange_chars_like:_(!@#$%^&*)_remain_unchanged',
               Str::snake('All the strangeCharsLike:   (!@#$%^&*) remain unchanged'));
        // with multibyte strings
        $this->assertSame('malmö_jönköping', Str::snake('Malmö Jönköping'));
        // with multibyte caps
        $this->assertSame('łukasz.żądełko', Str::snake('Łukasz.Żądełko'));
        $this->assertSame('łukasz_żądełko', Str::snake('ŁukaszŻądełko'));
    }

    public function testSnakeIden()
    {
        $this->assertSame('laravel_php_framework', Str::snakeIden('LaravelPHPFramework'));
        $this->assertSame('laravel_php_framework', Str::snakeIden('LaravelPhpFramework'));
        $this->assertSame('laravel php framework', Str::snakeIden('LaravelPhpFramework', ' '));
        $this->assertSame('laravel_php_framework', Str::snakeIden('Laravel Php Framework'));
        $this->assertSame('laravel_php_framework', Str::snakeIden('Laravel    Php      Framework   '));
        // ensure cache keys don't overlap
        $this->assertSame('laravel__php__framework', Str::snakeIden('LaravelPhpFramework', '__'));
        $this->assertSame('laravel_php_framework_', Str::snakeIden('LaravelPhpFramework_', '_'));
        $this->assertSame('laravel_php_framework', Str::snakeIden('laravel php Framework'));
        $this->assertSame('laravel_php_frame_work', Str::snakeIden('laravel php FrameWork'));
        // with weird whitespaces
        $this->assertSame('laravel_php_framework', Str::snakeIden("\v  Laravel \n\t   Php \r \n \v     Framework   "));
        $this->assertSame('__laravel_php_framework__', Str::snakeIden(' - Laravel  Php Framework    _    '));
        $this->assertSame('__laravel_php_framework__', Str::snakeIden(' / Laravel  Php Framework    ?    '));
        // with numbers
        $this->assertSame('laravel6_php_frame_work', Str::snakeIden('laravel6 php FrameWork'));
        $this->assertSame('laravel6_php_framework', Str::snakeIden('laravel6PHPFramework'));
        // from camelCase
        $this->assertSame('laravel_php_framework', Str::snakeIden('laravelPhpFramework'));
        // from kebab-case
        $this->assertSame('laravel_php_framework', Str::snakeIden('laravel-php-framework'));
        // from Snake_Caps
        $this->assertSame('laravel_php_framework', Str::snakeIden('Laravel_Php_Framework'));
        // from Kebab-Caps
        $this->assertSame('laravel_php_framework', Str::snakeIden('Laravel-Php-Framework'));
        // from Url
        $this->assertSame('http___hello_world_org', Str::snakeIden('http://HelloWorld.org'));
        // from camelCase.nestedMember
        $this->assertSame('i_am_the_nested_member', Str::snakeIden('iAm.theNested.member'));
        // from StudlyCase.NestedMember
        $this->assertSame('i_am_the_nested_member', Str::snakeIden('IAm.TheNested.Member'));
        // from kebab-case.nested-member
        $this->assertSame('i_am_the_nested_member', Str::snakeIden('i-am.the-nested.member'));
        // from strings with wildcards (I've seen this somewhere in laravel/framework)
        $this->assertSame('i_am___last_member', Str::snakeIden('iAm.*.lastMember'));
        // with strange chars
        $this->assertSame('all_the_strange_chars_like_____________get_replaced',
               Str::snakeIden('All the strangeCharsLike:   (!@#$%^&*) get/replaced'));
        // with multibyte strings
        $this->assertSame('malmö_jönköping', Str::snakeIden('Malmö Jönköping'));
        // with multibyte caps
        $this->assertSame('łu_kasz_żą_dełko', Str::snakeIden('ŁuKasz.ŻąDełko'));
        $this->assertSame('łukasz_żądełko', Str::snakeIden('ŁukaszŻądełko'));
    }

    public function testStudly()
    {
        $this->assertSame('LaravelPHPFramework', Str::studly('laravel_p_h_p_framework'));
        $this->assertSame('LaravelPhpFramework', Str::studly('laravel_php_framework'));
        $this->assertSame('LaravelPhPFramework', Str::studly('laravel-phP-framework'));
        $this->assertSame('LaravelPhpFramework', Str::studly('laravel  -_-  php   -_-   framework   '));

        $this->assertSame('FooBar', Str::studly('fooBar'));
        $this->assertSame('FooBar', Str::studly('foo_bar'));
        $this->assertSame('FooBar', Str::studly('foo_bar')); // test cache
        $this->assertSame('FooBarBaz', Str::studly('foo-barBaz'));
        $this->assertSame('FooBarBaz', Str::studly('foo-bar_baz'));
    }

    public function testCamel()
    {
        $this->assertSame('laravelPHPFramework', Str::camel('Laravel_p_h_p_framework'));
        $this->assertSame('laravelPhpFramework', Str::camel('Laravel_php_framework'));
        $this->assertSame('laravelPhPFramework', Str::camel('Laravel-phP-framework'));
        $this->assertSame('laravelPhpFramework', Str::camel('Laravel  -_-  php   -_-   framework   '));

        $this->assertSame('fooBar', Str::camel('FooBar'));
        $this->assertSame('fooBar', Str::camel('foo_bar'));
        $this->assertSame('fooBar', Str::camel('foo_bar')); // test cache
        $this->assertSame('fooBarBaz', Str::camel('Foo-barBaz'));
        $this->assertSame('fooBarBaz', Str::camel('foo-bar_baz'));
    }

    public function testSubstr()
    {
        $this->assertSame('Ё', Str::substr('БГДЖИЛЁ', -1));
        $this->assertSame('ЛЁ', Str::substr('БГДЖИЛЁ', -2));
        $this->assertSame('И', Str::substr('БГДЖИЛЁ', -3, 1));
        $this->assertSame('ДЖИЛ', Str::substr('БГДЖИЛЁ', 2, -1));
        $this->assertEmpty(Str::substr('БГДЖИЛЁ', 4, -4));
        $this->assertSame('ИЛ', Str::substr('БГДЖИЛЁ', -3, -1));
        $this->assertSame('ГДЖИЛЁ', Str::substr('БГДЖИЛЁ', 1));
        $this->assertSame('ГДЖ', Str::substr('БГДЖИЛЁ', 1, 3));
        $this->assertSame('БГДЖ', Str::substr('БГДЖИЛЁ', 0, 4));
        $this->assertSame('Ё', Str::substr('БГДЖИЛЁ', -1, 1));
        $this->assertEmpty(Str::substr('Б', 2));
    }

    public function testUcfirst()
    {
        $this->assertSame('Laravel', Str::ucfirst('laravel'));
        $this->assertSame('Laravel framework', Str::ucfirst('laravel framework'));
        $this->assertSame('Мама', Str::ucfirst('мама'));
        $this->assertSame('Мама мыла раму', Str::ucfirst('мама мыла раму'));
    }

    public function testUuid()
    {
        $this->assertInstanceOf(UuidInterface::class, Str::uuid());
        $this->assertInstanceOf(UuidInterface::class, Str::orderedUuid());
    }
}

class StringableObjectStub
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
