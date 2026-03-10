
## 2026-03-03 - N+1 issue using Spatie hasRole and multiple aggregated queries
**Learning:** Using `hasRole` on users fetched without eager-loading roles triggers N+1 queries. In addition, looping over Carbon months and issuing a separate aggregate `count` query each iteration dramatically multiplies DB overhead.
**Action:** Always fetch `User::with('roles')` before looping to check roles. When creating timeline-based statistics, aggregate the whole block at the database level with a `GROUP BY` and a DB-agnostic grouped query instead of making separate queries in PHP loop.
