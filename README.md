Kitap ticaret sitesi, kullanıcıların kitap satın alabileceği ve satıcıların satış yapabileceği bir e-ticaret platformudur. Kullanıcılar üye olup kitap kategorisine, yazar ve fiyat gibi değişikliklere göre arayabilir, sepetlerine depolanmış güvenli bir şekilde satın alabilirler. Yönetici paneli sayesinde yöneticilerin kitap ve siparişleri yönetilebilir, 
kullanıcı işlemleri kontrol edilebilir. Sistem, HTML, CSS ve JavaScript ile geliştirilmiş olup, PHP ile dinamik içerik yönetimi sağlanmıştır.
SQL Server veri tabanı kullanılarak kitap, kullanıcı ve sipariş bilgileri saklanmaktadır. OOP lisansı sayesinde modüler ve ölçeklenebilir bir yapı sunulmaktadır.
1-  Index
Bu, web sitesinin ana sayfasıdır. Kullanıcılar siteye giriş yaptıklarında ilk olarak bu sayfayla karşılaşır. Giriş yapılmamışsa, ziyaretçilere genel bir ana ekran sunar ve kullanıcıları giriş yapmaya ya da üye olmaya teşvik eder. Giriş yapıldığında ise kişiselleştirilmiş bir ana ekran olarak çalışır ve kullanıcıların alışveriş deneyimini başlatmasına olanak tanır.
2-  Giriş
Kullanıcıların sisteme kayıtlı hesaplarıyla giriş yapmasını sağlayan ekrandır. Burada kullanıcı adı ve şifre gibi bilgilerin doğrulanması yapılır. Hatalı girişlerde kullanıcılara hata mesajları gösterilir ve şifre sıfırlama ya da kayıt olma seçenekleri sunulabilir.
3-  UyeOlkitap
Kullanıcıların yeni bir hesap oluşturması için kullanılan formdur. Kullanıcı adı, e-posta adresi, şifre ve diğer gerekli bilgiler burada alınır. Ayrıca kullanıcıların bilgilerini doğru girdiğinden emin olmak için doğrulama mekanizmaları bulunabilir.
4-  İletişim
Kullanıcıların site yöneticileriyle iletişim kurabilmesi için hazırlanmış bir formdur. Kullanıcılar, ad, e-posta ve mesajlarını yazarak herhangi bir sorunu ya da öneriyi bu form üzerinden iletebilir. Geri bildirimler genellikle e-posta yoluyla yöneticilere ulaşır.
5-  Kitap_Detay
Kullanıcılara, kitapların başlığı, yazarı, fiyatı, kısa açıklaması gibi ayrıntılı bilgileri gösteren bir sayfadır. Ayrıca bu sayfadan kitabı sepete ekleme veya satın alma işlemi yapılabilir.
6-  KitapAdmin
Yalnızca admin yetkisine sahip kullanıcıların erişebileceği bir paneldir. Bu sayfa üzerinden normal kitaplar yönetilir. Admin, kitap ekleme, düzenleme veya silme işlemlerini gerçekleştirebilir. Ayrıca mevcut stok bilgileri ve kitap detayları da buradan görüntülenir.
7-  Seslikitap
Kullanıcılara sunulan sesli kitapların listelendiği bir sayfadır. Kullanıcılar, dinlemek istedikleri sesli kitabı seçebilir ve mevcutsa önizleme seslerini dinleyebilir. Ayrıca sesli kitapları sepete eklemek veya satın almak için seçenekler bulunur.
8-  SeslikitapAdmin
Adminin sesli kitaplarla ilgili tüm işlemleri yapabildiği bir paneldir. Sesli kitapların sisteme eklenmesi, düzenlenmesi veya silinmesi gibi işlemler bu ekrandan gerçekleştirilir. Ayrıca sesli kitapların detay bilgileri de yönetilebilir.
9-  Detay
Sesli kitapların açıklamalarını ve ek bilgilerini içeren bir sayfadır. Kullanıcılar, seçtikleri sesli kitap hakkında daha fazla bilgi alabilirler.
10-  Sepet
Kullanıcıların alışveriş sırasında seçtikleri kitapları listeleyen bir formdur. Bu sayfada kullanıcı, sepete eklediği ürünleri görüntüleyebilir, ürün sayısını artırıp azaltabilir veya tamamen sepetten çıkarabilir.
11-  Sepetim
Kullanıcıların alışveriş sepetine giderek tüm seçili ürünlerin bir özetini görüntülediği sayfadır. Burada toplam tutar, kitap adları ve fiyatları gibi bilgiler gösterilir.
12-  Sepeim
Sepet işlemleriyle ilgili ek fonksiyonların yer aldığı bir formdur. Örneğin, sepet temizleme veya sepeti güncelleme gibi işlemler bu formda yapılabilir.
13-  Siparis_Onayla
Kullanıcıların sepetindeki ürünleri onayladığı ve sipariş oluşturduğu formdur. Siparişin tamamlanabilmesi için kullanıcıdan teslimat adresi, iletişim bilgileri ve ödeme bilgileri istenir. Sipariş detaylarının özetlendiği bir onay ekranı da bulunabilir.
14-  Ödeme
Kullanıcıların ödeme işlemlerini tamamladığı ekran. Burada kredi kartı bilgileri ya da diğer ödeme yöntemleri alınır ve ödeme doğrulaması yapılır. 
15-  Siparişler
Kullanıcıların geçmiş siparişlerini görüntülediği bir sayfadır. Sipariş detayları, teslimat durumları ve sipariş tarihleri gibi bilgiler burada yer alır. Kullanıcı, her siparişi ayrı ayrı inceleyebilir ve destek talebinde bulunabilir.

