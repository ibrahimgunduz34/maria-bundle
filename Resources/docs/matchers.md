## Matchers:

[Back to home page](/)

### ALL:
```yaml
  # ...
  rules:
    all:
      - amount: { gt: 100 }
  # ...    
```

### ANY:
```yaml
  # ...
  rules:
    any:
      - amount: { gt: 100 }
  # ...    
```

### NONE:
```yaml
  # ...
  rules:
    none:
      - amount: { gt: 100 }
  # ...    
```
 
 ### FIRST:
 ```yaml
   # ...
   rules:
     first:
       - amount: { gt: 100 }
   # ...    
 ```
 
 ### LAST:
  ```yaml
    # ...
    rules:
      last:
        - amount: { gt: 100 }
    # ...    
  ```
  
  ### DEFAULT:

```yaml
  # ...
  rules:
      - amount: { gt: 100 }
  # ...    
```
or

```yaml
  # ...
  rules:
    default:
      - amount: { gt: 100 }
  # ...    
```