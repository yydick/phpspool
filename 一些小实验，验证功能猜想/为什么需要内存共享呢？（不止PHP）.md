PHP如果只运行在FPM下，是不怎么需要内存共享的，连fork都不能用。
但是咸鱼也是有梦想的，大家总想搞点儿多线程、多进程、携程……错了，是协程！
做点儿看起来很牛逼的事儿！
可是牛逼是牛逼了，怎么调度呢？解决方案有很多，但都不如内存共享用着爽！

给大家讲个故事吧。

我叫PHP，是一个勤劳的农民，每天早起种田，日落回家睡觉，干得不错，赚钱了。
赚钱了干嘛呢？买地啊！

于是，我在后山买到了一块地。不是不想就近买，旁边的地都有主了，人不卖我。
我就开始每天两块地来回跑着干活（分时处理），那个累啊，累的跟条狗似的。
为了不累成死狗，我决定，fuck个……啊，是fork个儿子。
儿子随我，干活也挺麻利，就是经验少，总得我指导他干活，我敢放手，
他就敢变成僵尸（进程）！
我不敢放手，只好一边儿干活，一边儿指导他。一开始很艰苦，只有个消息树（signal），
除了能传递个鬼子来了、儿子死了、老子死了之类的，也干不了啥。
后来我想了一招儿，找了个孙子（不是我儿子fuck的），名字也古怪，一会儿叫MQ，
一会儿叫DB，一会儿叫CACHE，总之，话能说利索了，跑个腿还行，就是有点儿笨，
传了什么话儿，他就先记着，你要是不问他，他到死都不会告诉你，所以啊，你要么
得空就问问他（轮训），要么就告诉他，有什么重要的事儿得主动告诉我（回调），
不然就别指着他干活。
后来呢，钱赚多了，又买了好几块地，都不在一块儿，我就又f**k了几个儿子，一块儿干活。
传话那孙子越来越笨了，传个话有时候都能传错，我非常不爽。
邻居老C，C艹和贾维斯（java），都有个叫线程的仓库，要传个话什么的，
在仓库里共享一下，就都知道了，干活那叫一个爽利！我不行，祖上没传下来这个手艺啊。
所以我就自己弄了个纠缠态双子星什么的，管他叫什么，反正老子这里头痛了，
就有儿子来送药，不用我打电话去催，挺好的。
以后会怎样？谁知道呢。
