# Schemer changelog

## 0.3.0 (2013-06-25)

* **[BC]** Defaulting validator interface removed.
* **[BC]** Defaulting validator no longer needs to use
  validateAndApplyDefaults(), just use validate() instead.
* **[NEW]** Implemented validating reader (#13).
* **[IMPROVED]** Made bound constraint validator's inner validator an optional
  dependency (#14).
* **[IMPROVED]** Validator validate() method now takes value as a reference,
  allowing defaulting validators to share the same interface.
* **[IMPROVED]** Bound constraint validator can now use a defaulting validator
  internally.
* **[IMPROVED]** Restricted Zend URI integration to Uri namespace.
* **[IMPROVED]** Value factory can now handle creation of recursive structures.
* **[IMPROVED]** Value instances can now return their native equivalents, even
  when circular references are present.

## 0.2.0 (2013-05-15)

* **[NEW]** Implemented resolver capable of dealing with switch resolution scope
  via the 'id' keyword.
* **[IMPROVED]** Schemer meta-schema refactored.
* **[NEW]** Implemented resolving reader.
* **[NEW]** Implemented defaulting validator (#9).

## 0.1.0 (2013-05-07)

* Initial implementation.
