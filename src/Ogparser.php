<?php
namespace nkwtnb\ogparser;

use nkwtnb\ogparser\OGPError;
use Exception;

class Ogparser {

  protected $url;
  protected $title;
  protected $description;
  protected $image;
  protected $site_name;

  public function __construct(string $url) {
    // you can access only HTTPS sites.
    if (preg_match('/^https:\/\/.*/', $url) === 0) {
      throw new Exception(
        OGPError::$MSG["ONLY_HTTPS"]
      );
    }
    $this->url = $url;
  }

  public function fetch() {
    $curl = curl_init($this->url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
    $html = curl_exec($curl);
    $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($status_code !== 200) {
      if ($status_code === 0) {
        throw new Exception(
          OGPError::$MSG["SITE_NOT_FOUND"],
          $status_code
        );
      } else if($status_code === 404) {
        throw new Exception(
          OGPError::$MSG["PAGE_NOT_FOUND"],
          $status_code
        );
      }
    }

    $dom = new \DOMDocument();
    $from_encoding = mb_detect_encoding(
      $html,
      [
        'ASCII',
        'UTF-8',
        'EUC-JP',
        'SJIS',
      ],
      true
    );
    if (!$from_encoding) {
      $from_encoding = 'UTF-8';
    }
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $from_encoding));
    $xml_object = simplexml_import_dom($dom);
    $this->title = $this->find_title($xml_object);
    $this->description = $this->find_description($xml_object);
    $this->image = $this->find_image($xml_object);
    $this->site_name = $this->find_site_name($xml_object);
    curl_close($curl);
  }

  /**
   * DOMからタイトルを取得
   */
  private function find_title($xml_object)
  {
    $value = "";
    $og_tag = $xml_object->xpath('//meta[@property="og:title"]/@content');
    if (count($og_tag) > 0) {
      $value = $og_tag[0];
    } else {
      $title_tag = $xml_object->xpath('//title/text()');
      if (count($title_tag) > 0) {
        $value = $title_tag[0];
      }
    }
    return (string)$value;
  }

  /**
   * DOMから説明を取得
   */
  private function find_description($xml_object)
  {
    $value = "";
    $og_tag = $xml_object->xpath('//meta[@property="og:description"]/@content');
    if (count($og_tag) > 0) {
      $value = $og_tag[0];
    } else {
      $meta_description_tag = $xml_object->xpath('//meta[@name="description"]/@content');
      if (count($meta_description_tag) > 0) {
        $value = $meta_description_tag[0];
      }
    }
    return (string)$value;
  }
  /**
   * DOMからサムネイル画像を取得
   */
  private function find_image($xml_object)
  {
    $value = "";
    $og_tag = $xml_object->xpath('//meta[@property="og:image"]/@content');
    if (count($og_tag) > 0) {
      $value = $og_tag[0];
    } else {
      $meta_thubnail_tag = $xml_object->xpath('//meta[@name="thumbnail"]/@content');
      if (count($meta_thubnail_tag) > 0) {
        $value = $meta_thubnail_tag[0];
      }
    }
    return (string)$value;
  }
  /**
   * DOMからサイト名を取得
   */
  private function find_site_name($xml_object)
  {
    $value = "";
    $og_tag = $xml_object->xpath('//meta[@property="og:site_name"]/@content');
    if (count($og_tag) > 0) {
      $value = $og_tag[0];
    } else {
      $meta_thubnail_tag = $xml_object->xpath('//meta[@name="site_name"]/@content');
      if (count($meta_thubnail_tag) > 0) {
        $value = $meta_thubnail_tag[0];
      }
    }
    return (string)$value;
  }
  /**
   * URLを取得
   */
  public function get_url() {
    return $this->url;
  }
  /**
   * タイトルを取得
   */
  public function get_title() {
    return $this->title;
  }
  /**
   * 説明を取得
   */
  public function get_description() {
    return $this->description;
  }
  /**
   * サムネイル画像を取得
   */
  public function get_image() {
    return $this->image;
  }
  /**
   * サイト名を取得
   */
  public function get_site_name() {
    return $this->site_name;
  }
}
