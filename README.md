REMM
Aplikasi pembuat jadwal kuliah berbasis web menggunakan algoritma semut (And Colony Optimization)

Untuk mengacak jadwal seperti yang dilakukan koloni semut, maka harus diketahui terlebih dahulu graf hubungan antara mata kuliah, ruangan dan waktu. Graf tersebut dapat digambarkan sebagai matriks di bawah ini.

MK1R1T1 MK2R1T1 ....... MKnR1T1

MK1R1T2 MK2R1T2 ....... MKnR1T2

....... ....... ....... .......

MK1R1Tn MK2R1Tn ....... MKnR1Tn

MK1R2T1 MK2R2T1 ....... MKnR2T1

MK1R2T2 MK2R2T2 ....... MKnR2T2

....... ....... ....... .......

MK1RnTn MK2RnTn ....... MKnRnTn

Keterangan:
MK = Mata Kuliah
R  = Ruangan
T  = Timeslot

Matriks di atas dapat dibuat graf dengan menghubungkan setiap data yang terdapat di dalamnya. Data-data pada matriks di atas kemudian bisa disebut dengan node untuk membuat graf.

Semut pertama akan mencari node pada kolom pertama secara acak untuk jadwal mata kuliah pertama. Jika node yang didapat sesuai dengan ketentuan (misalnya dosen mata kuliah pertama sedang tidak mengajar pada timeslot di node tersebut atau ruangan yang akan digunakan sedang tidak dipakai pada node tersebut), maka node tersebut akan di-update pheromone-nya. Namun jika node yang didapat tidak sesuai dengan ketentuan (misalnya dosen mata kuliah pertama sedang mengajar pada timeslot di node tersebut atau ruangan yang akan digunakan sedang dipakai pada node tersebut), maka semut akan mencari lagi node lain secara acak yang masih di kolom tersebut sampai mendapatkan node yang sesuai dengan ketentuan.

Setelah mendapatkan node pada kolom pertama, semut akan mencari node lagi pada kolom kedua dan seterusnya sampai kolom terakhir. Setelah mendapatkan node pada kolom terakhir, daftar node tersebut dapat disebut dengan jalur. Kemudian semut lain akan mencari jalur seperti yang dilakukan oleh semut pertama.

Setelah beberapa semut mendapatkan jalur, semut berikutnya dapat memilih untuk mencari jalur sendiri atau mengikuti jalur semut yang sudah mendapatkan jalur dengan mencari pheromone yang paling kuat.

Jalur yang paling banyak digunakan oleh semut inilan yang akan dijadikan pertimbangan untuk mendapatkan jalur yang optimal.
