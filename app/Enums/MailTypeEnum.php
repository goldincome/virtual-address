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
  public static function toArray(): array
  {
    return array_column(self::cases(), 'value');
  }


}