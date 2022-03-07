<?php

namespace nkwtnb\ogparser\test;

use Exception;
use nkwtnb\ogparser\Ogparser;
use PHPUnit\Framework\TestCase;

class OgparserTest extends TestCase {
  // URL形式ではない
  public function testInvalidURL() {
    $this->expectException(Exception::class);
    new Ogparser("abcde");
  }
  // HTTPSではない
  public function testNotHttpsURL() {
    $this->expectException(Exception::class);
    new Ogparser("http://localhost");
  }
  // サイトが存在しない
  public function testNotExistsURL() {
    $this->expectException(Exception::class);
    $ogp = new Ogparser("https://localhost-not-exists");
    $ogp->fetch();
  }
  // ページが存在しない
  public function testNotExistsPage() {
    $this->expectException(Exception::class);
    $ogp = new Ogparser("https://www.google.com/not-exists");
    $ogp->fetch();
  }
  /**
   * @doesNotPerformAssertions
   */
  // OGPの取得処理が実行できる
  public function testCanGetOGP() {
    $_URL = getenv("URL");
    throw new Exception("specify target url. ex)URL=https://www.example.com phpunit tests");
    $ogp = new Ogparser($_URL);
    $ogp->fetch();
    var_dump(<<<EOM

{$ogp->get_url()}
{$ogp->get_title()}
{$ogp->get_description()}
{$ogp->get_image()}
{$ogp->get_site_name()}
EOM);
  }
}
