<?php

namespace Thor\Common;

class Cipher
{

    private static ?self $aes256 = null;

    public const RAW = OPENSSL_RAW_DATA;
    public const PAD = OPENSSL_ZERO_PADDING;

    public function __construct(
        public ?string $passphrase = null,
        public ?string $iv = null,
        public ?string $tag = null,
        public readonly string $cipher_algo = 'aes-256-gcm',
        public readonly bool $option_raw = true,
        public readonly bool $option_pad = false,
        public readonly int $tag_length = 16,
        public readonly string $aad = '',
    ) {
        if ($this->passphrase === '') {
            $this->passphrase = Guid::hex();
        }
        if ($this->iv === null) {
            $len = openssl_cipher_iv_length($this->cipher_algo);
            $this->iv = openssl_random_pseudo_bytes($len);
        }
    }

    public function encrypt(
        string $data
    ): string|false {
        $this->tag = null;
        return openssl_encrypt(
            $data,
            $this->cipher_algo,
            $this->passphrase,
            ($this->option_raw ? self::RAW : 0) | ($this->option_pad ? self::PAD : 0),
            $this->iv,
            $this->tag,
            $this->aad,
            $this->tag_length
        );
    }

    public function decrypt(
        string $data
    ): string|false {
        return openssl_decrypt(
            $data,
            $this->cipher_algo,
            $this->passphrase,
            ($this->option_raw ? self::RAW : 0) | ($this->option_pad ? self::PAD : 0),
            $this->iv,
            $this->tag,
            $this->aad
        );
    }

    public static function setInstance(string $passphrase): void
    {
        self::$aes256 = new self($passphrase, option_raw: false);
    }

    public static function getInstance(): self
    {
        return self::$aes256 ??= new self(Guid::hex(), option_raw: false);
    }

    public static function cipher(string $data): string
    {
        return self::getInstance()->encrypt($data);
    }

    public static function decipher(string $data): string
    {
        return self::getInstance()->decrypt($data);
    }

}
