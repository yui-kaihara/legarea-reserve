{{ $guest->name }}様

お世話になっております。
LEGAREA事務局です。

この度は当社の交流会にお申し込みいただき、ありがとうございます。
下記内容にて申込みをお受付いたしました。

氏名：{{ $guest->name }}様
開催：{{ $guest->event->date->isoFormat('Y年M月D日(ddd)') }}　
会場：{{ $guest->event->place }}

受付：{{ $guest->event->start_time->format('H:i') }}~※受け付け順に交流開始
終了：~{{ $guest->event->end_time->format('H:i') }}
金額：{{ ($guest->company->count > 1) ? $guest->event->amount.'円/1人': '無料' }}
定員：{{ $guest->event->capacity }}名(1社につき2名まで)

※SES交流会となりますので、関係のない営業を発見した場合は退場していただきます。
　また無理な営業なども同じように対応させていただきますので、あらかじめご了承ください。
※お名刺をお持ちください。
　受付時に一枚頂戴いたしますので、余裕を持ってお持ちください。

■緊急連絡先：
LEGAREA小野村 （k-onomura@legarea.jp）
Tel：070-2314-5927

※無断キャンセルはキャンセル代かかります※