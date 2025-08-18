<?php
namespace App\Enums;

enum ProductTypeEnum: string
{
    case VIRTUAL_ADDRESS = 'virtual_address';
    case MEETING_ROOM = 'meeting_room';
    case CONFERENCE_ROOM = 'conference_room';
    case MAIL_SERVICE = 'mail_service';

    public static function available(): array
    {
        return [
            self::VIRTUAL_ADDRESS,
            self::MEETING_ROOM,
            self::CONFERENCE_ROOM,
            self::MAIL_SERVICE,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::VIRTUAL_ADDRESS => 'Virtual Address',
            self::MEETING_ROOM => 'Meeting Room',
            self::CONFERENCE_ROOM => 'Conference Room',
            self::MAIL_SERVICE => 'Mail Scanning/Forwarding',
        };
      
    }
}