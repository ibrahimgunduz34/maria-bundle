## Arithmetic And Logic Operators

[Back to home page](/README.md)

### Greater Than X

```yaml
# ...
  rules:
    - amount: { gt: 100 }
# ...
```

### Less Than X

```yaml
# ...
  rules:
    - amount: { lt: 100 }
# ...
```

### Greater Than Or EqualX

```yaml
# ...
  rules:
    - amount: { gte: 100 }
# ...
```

### Less Than Or Equal X

```yaml
# ...
  rules:
    - amount: { lte: 100 }
# ...
```

### Between

```yaml
# ...
  rules:
    - amount: { btw: [100, 500] }
# ...
```

### Regex

```yaml
# ...
  rules:
    - description: { regex: /awesome/i }
# ...
```

### In [...]
```yaml
# ...
  rules:
    - category_id: { in: [1,2,3] }
# ...
```
### And

```yaml
# ...
  rules:
    - amount: { gt: 100 }
      category_id: { in: [1,2,3] }
# ...
```

### Or

```yaml
# ...
  rules:
    - category_id: { eql: 10 }
    - category_id: { eql: 5 }
# ...
```








