<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Produits & Catégories</title>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Produits</h1>
    <ul id="products"></ul>

    <h1>Catégories</h1>
    <ul id="categories"></ul>

    <script>
        axios.get('/api/products')
            .then(res => {
                const list = document.getElementById('products');
                res.data.forEach(p => {
                    const li = document.createElement('li');
                    li.textContent = `${p.name} (Catégorie: ${p.category?.name || 'N/A'})`;
                    list.appendChild(li);
                });
            })
            .catch(err => console.error(err));

        axios.get('/api/categories')
            .then(res => {
                const list = document.getElementById('categories');
                res.data.forEach(c => {
                    const li = document.createElement('li');
                    li.textContent = c.name;
                    list.appendChild(li);
                });
            })
            .catch(err => console.error(err));
    </script>
</body>
</html>
