## **Kitap Yönetim Sistemi API**

Bu proje, Laravel ile geliştirilmiş bir **RESTful API**’dir. API, kitapların eklenmesi, güncellenmesi, silinmesi ve listelenmesi gibi temel CRUD işlemlerini gerçekleştirir.

---

### **Proje Özellikleri**
- **Kitap Ekleme**: `/api/books` (POST)
- **Kitap Listeleme**: `/api/books` (GET)
- **Tek Bir Kitap Görüntüleme**: `/api/books/{id}` (GET)
- **Kitap Güncelleme**: `/api/books/{id}` (PUT/PATCH)
- **Kitap Silme**: `/api/books/{id}` (DELETE)

---

### **Ekran Görüntüleri**

Pest Test
![](/screenshots/Pest_Test.png)
ThunderClient Collection Testi
![](/screenshots/thunderclient.png)
Örnek bir kitap oluşturma
![](/screenshots/exampleCreate.png)
---
### **Kurulum**

#### 1. **Proje Bağımlılıklarını Yükleyin**

Laravel ve diğer bağımlılıkları yüklemek için aşağıdaki komutları kullanabilirsiniz.

```bash
composer install
```

#### 2. **Veritabanı Bağlantısını Yapın**

Veritabanını bağlantı bilgilerini `.env` dosyasına ekleyin. MySQL için aşağıdaki gibi yapılandırabilirsiniz:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kitap_yonetim_sistemi
DB_USERNAME=root
DB_PASSWORD=
```

#### 3. **Veritabanı Oluşturma**

Veritabanını oluşturmak için MySQL üzerinden aşağıdaki komutu çalıştırın:

```mysql
create database kitap_yonetim_sistemi ;
```

#### 4. **Migrasyonları Çalıştırın**

Veritabanı tablolarını oluşturmak için aşağıdaki komutu çalıştırın:

```bash
php artisan migrate
```

#### 5. **API Servisini Çalıştırın**

API'yi çalıştırmak için aşağıdaki komutu kullanabilirsiniz:

```bash
php artisan serve
```

Alternatif olarak, yerel geliştirme sunucusunu başlatabilirsiniz:

```bash
php -S localhost:8000 -t public/
```

#### 6. **Geliştirme Ortamını Başlatın**

Frontend veya diğer geliştirme araçlarını çalıştırmak için:

```bash
composer run dev
```

---

### **Testler**

Projenin testlerini yazdınız ve aşağıda her bir testin ne yaptığına dair bir açıklama bulabilirsiniz. Testler, **Pest** ile yazılmıştır.

#### **Test Senaryoları:**

1. **Yeni Kitap Ekleme**

Yeni bir kitap eklemek için `/api/books` endpoint'ine POST isteği gönderilir. Kitap başarıyla eklenirse, dönen yanıtta doğru veriler yer almalıdır.

```php
it('can create a new book', function () {
    $response = $this->postJson('/api/books', [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'description' => 'This is a test book description.',
    ]);

    $response->assertStatus(201);
    $book = $response->json();
    expect($book['title'])->toBe('Test Book');
    expect($book['author'])->toBe('Test Author');
    expect($book['description'])->toBe('This is a test book description.');
});
```

2. **Kitap Listesini Getirme**

Veritabanına 3 kitap eklenir ve `/api/books` endpoint'ine GET isteği yapılır. Dönen cevapta en az 3 kitap olmalıdır.

```php
it('can retrieve a list of books', function () {
    Book::factory()->count(3)->create();
    $response = $this->getJson('/api/books');
    $response->assertStatus(200);
    expect(count($response->json()))->toBeGreaterThanOrEqual(3);
});
```

3. **Tek Bir Kitap Görüntüleme**

Veritabanında bir kitap oluşturulur ve ID'sine göre `/api/books/{id}` endpoint'ine GET isteği yapılır. Dönen cevabın doğru kitabı içerdiği kontrol edilir.

```php
it('can retrieve a single book by id', function () {
    $book = Book::factory()->create();
    $response = $this->getJson("/api/books/{$book->id}");
    $response->assertStatus(200);
    expect($response->json('title'))->toBe($book->title);
    expect($response->json('author'))->toBe($book->author);
});
```

4. **Mevcut Olmayan Kitap İçin 404 Döndürme**

Var olmayan bir kitap ID'si ile `/api/books/{id}` endpoint'ine GET isteği yapılır. API 404 döndürmelidir.

```php
it('returns 404 when trying to retrieve a non-existing book', function () {
    $response = $this->getJson('/api/books/999999');
    $response->assertStatus(404);
    $response->assertJson(['message' => 'Book not found']);
});
```

5. **Kitap Güncelleme**

Var olan bir kitabın bilgileri güncellenir ve `/api/books/{id}` endpoint'ine PUT isteği yapılır. Güncellenen verilerin doğru olması kontrol edilir.

```php
it('can update an existing book', function () {
    $book = Book::factory()->create();
    $response = $this->putJson("/api/books/{$book->id}", [
        'title' => 'Updated Book Title',
        'author' => 'Updated Author',
        'description' => 'Updated description.',
    ]);

    $response->assertStatus(200);
    $updatedBook = $response->json();
    expect($updatedBook['title'])->toBe('Updated Book Title');
    expect($updatedBook['author'])->toBe('Updated Author');
    expect($updatedBook['description'])->toBe('Updated description.');
});
```

6. **Mevcut Olmayan Kitap İçin Güncelleme Yapma**

Var olmayan bir kitap ID'si ile `/api/books/{id}` endpoint'ine PUT isteği yapılır. API 404 döndürmelidir.

```php
it('returns 404 when trying to update a non-existing book', function () {
    $response = $this->putJson('/api/books/999999', [
        'title' => 'Updated Book Title',
        'author' => 'Updated Author',
    ]);
    $response->assertStatus(404);
    $response->assertJson(['message' => 'Book not found']);
});
```

7. **Kitap Silme**

Bir kitap silinir ve `/api/books/{id}` endpoint'ine DELETE isteği yapılır. Silme işlemi başarılı olduğunda, API başarılı yanıt döndürmelidir.

```php
it('can delete a book', function () {
    $book = Book::factory()->create();
    $response = $this->deleteJson("/api/books/{$book->id}");
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Book deleted Successfully']);
    $this->assertDatabaseMissing('books', ['id' => $book->id]);
});
```

8. **Mevcut Olmayan Kitap İçin Silme İşlemi**

Var olmayan bir kitap ID'si ile `/api/books/{id}` endpoint'ine DELETE isteği yapılır. API 404 döndürmelidir.

```php
it('returns 404 when trying to delete a non-existing book', function () {
    $response = $this->deleteJson('/api/books/999999');
    $response->assertStatus(404);
    $response->assertJson(['message' => 'Book not found']);
});
```

---

### **Proje Kullanımı**

Proje, kitapları yönetmek için kullanılabilir. API isteklerini **Postman** veya benzeri araçlar kullanarak kolayca test edebilirsiniz. API’nin kullanımını test etmek için yukarıdaki test senaryolarını da inceleyebilirsiniz.

---
