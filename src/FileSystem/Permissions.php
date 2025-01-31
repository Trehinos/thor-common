<?php

namespace Thor\Common\FileSystem;

/**
 * Documentation class.
 *
 * This class holds constants to help using POSIX/UNIX permissions.
 *
 * The format is an octal number "AAABCCC" with :
 *
 * - AAA : Type
 * - B : Special
 * - CCC : Permissions
 */
class Permissions
{

    // Type bits 'AAA'
    public const SOCKET = 0140000;
    public const SYMLINK = 0120000;
    public const REGULAR = 0100000;
    public const SPECIAL_BLOCK = 0060000;
    public const DIRECTORY = 0040000;
    public const SPECIAL_CHAR = 0020000;
    public const PIPE = 0010000;
    public const UNKNOWN = 0770000;

    // Special bits 'B'
    public const SET_UID = 04000;
    public const SET_GID = 02000;
    public const STICKY_BIT = 01000;

    // Permissions bits 'CCC'
    public const OWNER_READ = 0400;
    public const OWNER_WRITE = 0200;
    public const OWNER_EXEC = 0100;
    public const GROUP_READ = 0040;
    public const GROUP_WRITE = 0020;
    public const GROUP_EXEC = 0010;
    public const OTHER_READ = 0004;
    public const OTHER_WRITE = 0002;
    public const OTHER_EXEC = 0001;

    // Combinations
    public const ANY_READ = self::OWNER_READ | self::GROUP_READ | self::OTHER_READ;
    public const ANY_WRITE = self::OWNER_WRITE | self::GROUP_WRITE | self::OTHER_WRITE;
    public const ANY_EXEC = self::OWNER_EXEC | self::GROUP_EXEC | self::OTHER_EXEC;

    public const OWNER_READ_WRITE = self::OWNER_READ | self::OWNER_WRITE;
    public const OWNER_WRITE_EXEC = self::OWNER_EXEC | self::OWNER_WRITE;
    public const OWNER_READ_EXEC = self::OWNER_READ | self::OWNER_EXEC;
    public const OWNER_ALL = self::OWNER_READ_WRITE | self::OWNER_EXEC;

    public const GROUP_READ_WRITE = self::GROUP_READ | self::GROUP_WRITE;
    public const GROUP_WRITE_EXEC = self::GROUP_EXEC | self::GROUP_WRITE;
    public const GROUP_READ_EXEC = self::GROUP_READ | self::GROUP_EXEC;
    public const GROUP_ALL = self::GROUP_READ_WRITE | self::GROUP_EXEC;

    public const OTHER_READ_WRITE = self::OTHER_READ | self::OTHER_WRITE;
    public const OTHER_WRITE_EXEC = self::OTHER_EXEC | self::OTHER_WRITE;
    public const OTHER_READ_EXEC = self::OTHER_READ | self::OTHER_EXEC;
    public const OTHER_ALL = self::OTHER_READ_WRITE | self::OTHER_EXEC;

    public const ANY_ALL = self::ANY_READ | self::ANY_WRITE | self::ANY_EXEC;

    /**
     * Returns true if the file has at least the specified permissions.
     */
    public static function has(string $path, int $permission): ?bool
    {
        $pathPermissions = self::of($path);
        if (!is_int($pathPermissions)) {
            return null;
        }

        return ($pathPermissions & $permission) === $permission;
    }

    /**
     * Gets the file's permissions. If the file is not found, returns null. Returns false if an error occurs.
     */
    public static function of(string $path): int|false|null
    {
        if (!FileSystem::exists($path)) {
            return null;
        }
        return fileperms($path);
    }

    /**
     * Returns a permissions string like in a `ls -l` command.
     *
     * @example -rwxrw-r--
     * @example drwxr-x--x
     *
     * @link    https://www.php.net/manual/fr/function.fileperms.php#example-2167
     */
    public static function stringRepresentationFor(string $path): ?string
    {
        $perms = self::of($path);

        if (!is_int($perms)) {
            return null;
        }

        return match ($perms & 0770000) {
                   self::SOCKET => 's', // Socket
                   self::SYMLINK => 'l', // Symlink
                   self::REGULAR => '-', // Regular
                   self::SPECIAL_BLOCK => 'b', // Special block
                   self::DIRECTORY => 'd', // Directory
                   self::SPECIAL_CHAR => 'c', // Special character
                   self::PIPE => 'p', // Pipe
                   default => 'u', // Unknown
               } .
               self::permissionsStringFor($perms, self::OWNER_READ, self::OWNER_WRITE, self::OWNER_EXEC, self::SET_UID, 's', 'S') .
               self::permissionsStringFor($perms, self::GROUP_READ, self::GROUP_WRITE, self::GROUP_EXEC, self::SET_GID, 's', 'S') .
               self::permissionsStringFor($perms, self::OTHER_READ, self::OTHER_WRITE, self::OTHER_EXEC, self::STICKY_BIT, 't', 'T');
    }

    private static function permissionsStringFor(
        int $permission,
        int $readFilter,
        int $writeFilter,
        int $execFilter,
        int $specialFilter,
        string $specialStringOn,
        string $specialStringOff
    ): string {
        return (($permission & $readFilter) ? 'r' : '-') .
               (($permission & $writeFilter) ? 'w' : '-') .
               (($permission & $execFilter)
                   ? (($permission & $specialFilter) ? $specialStringOn : 'x')
                   : (($permission & $specialFilter) ? $specialStringOff : '-'));
    }

}
