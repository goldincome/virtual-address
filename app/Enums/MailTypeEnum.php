<?php
namespace App\Enums;



enum MailTypeEnum: string
{
    case Scanned = 'scanned';
    case Forwarded = 'forwarded';


    public function label(): string
    {
        return match ($this) {
            self::Scanned => 'Mail Scanning',
            self::Forwarded=> 'Mail Forwarding ',
        };
      
    }

    public function deliveryDescription(): string
    {
        return match ($this) {
            self::Scanned => 'All your mails will be scanned to you',
            self::Forwarded => 'All your mails will be forwarded to you',
        };
    }
  public static function toArray(): array
  {
    return array_column(self::cases(), 'value');
  }


}