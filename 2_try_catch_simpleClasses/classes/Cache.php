<?php


namespace Classes;


class Cache implements CacheInterface
{
    const SAVE_DIR = __DIR__.'/storage/cache';
    private static $error;

    /**
     * @param string $ident
     * @param mixed | string | integer | array $value
     * @param timestamp $ttl
     * @return false|int
     */
    public static function put(string $ident, $value, $ttl = null)
    {
        $filePath = self::SAVE_DIR . "/{$ident}.json";

        try {
            if (! is_dir(self::SAVE_DIR)) {
                $result = mkdir(self::SAVE_DIR, 775, true);
                if ($result === false) {
                    throw new \Exception('Can not make the directory.');
                }
            }
            return file_put_contents($filePath,  json_encode($value));
        } catch (\Exception $exception) {
            self::set_error($exception->getMessage());
        }
    }

    /**
     * @param $ident
     * @param null $default
     * @return mixed|null
     */
    public static function get($ident, $default = null)
    {
        $filePath = self::SAVE_DIR . "/{$ident}.json";

        try {
            if(self::has($ident)) {
                $result = (file_get_contents($filePath, true));
                if ($result !== false) {
                    $result = (array) json_decode($result);
                    return (count($result) > 1) ? $result : $result[0];
                } else {
                    throw new \FileNotFoundException('Function returns false on read attempt');
                }
            }
        } catch (\Exception $exception) {
            self::set_error($exception->getMessage());
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public static function delete($ident)
    {
        if(self::has($ident)) {
            $filePath = self::SAVE_DIR . "/{$ident}.json";

            unlink($filePath);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function clear()
    {
        // TODO: Implement clear() method.
    }

    /**
     * @inheritDoc
     */
    public static function getMultiple($idents, $default = null)
    {
        // TODO: Implement getMultiple() method.
    }

    /**
     * @inheritDoc
     */
    public static function putMultiple($values, $ttl = null)
    {
        // TODO: Implement putMultiple() method.
    }

    /**
     * @inheritDoc
     */
    public static function deleteMultiple($idents)
    {
        // TODO: Implement deleteMultiple() method.
    }

    /**
     * @inheritDoc
     */
    public static function has($ident)
    {
        $filePath = self::SAVE_DIR . "/{$ident}.json";
        if(file_exists($filePath)) {
            return true;
        }

        return false;
    }

    public static function get_error(){
        return self::$error;
    }

    public static function set_error($message){
        self::$error = $message;
        $file = fopen('log.txt','a');
        fwrite($file,"\r\n".gmdate('Y-m-d H:i:s').' ' .$message);
        fclose($file);
    }
}