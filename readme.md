### Requirements
- Install a command line tool, that can execute bash script. 
  - On windows use Gitbash https://git-scm.com/downloads
  
- Install Docker
  - On windows https://www.docker.com/docker-windows

##### Optional
- Install PHPStorm. Excellent tool for SQL, Mongo, HTML, PHP and Javascript.


##### Start up in dev mode
- `docker-compose up`

##### Bring down in dev mode
- `docker-compose down`

##### Utility scripts can be found in
- `cd bin/`

##### Test data can be found in
- `cd dumps/`

##### Run typescript watch is you are manipulating .ts files
- `sh bin/typescript-watch.sh`

### Code collaboration rules.
- Never call namespaced classes directly, use import statement "use"
- Never put @ infront of function call
- Never make one liners, extract into variables and pass to function call
- Never concatenate strings with ".", extract into variables and use "$userid is awesomesauce"
- Never lazy create object, or lazy insert data
  - If you must, don't call the function getUserById(), and magicly create/insert behind the curtains.
  - Ask questions and do operations instead.
- Never inject php into javascript files.
- Never use double equals (==), always use triple (===)
- Never superceed 140 chars, add a context interface, if params makes function too long
- Never use optional function arguments, to make a function do multiple things
  - Its better to have multiple explicit named functions
- Never use PHP_EOL, use \n (Unix line-break)
- Never write pure HTML as string, use .phtml extension
- Never call native php constructs directly.
  - define(), header(), echo(), print(), require(), include(), ob_start() etc. etc.
  - Encapsulate
- Never use php superglobals directly.
  - $_POST, $_COOKIE, $_SESSION, $_GET, $_REQUEST, $_FILES
  - $http_response_header
  - etc. etc.
- Never use public static variables
- Never optimize on your own
- Never do db queries inside while/for loop
- Never send context to children
  - Send specific scalar types (boolean, interger, float, string, array)
  - Make class specific context interface, if argument list gets to long
- Never use pseudo types (mixed, number, callback, array|object)
- Never add usecases, without adding regression test
- Never add tests, that test massive portions of the code
  - Prevents massive work in tests, when refactoring
- Keep tree structures flat, including folder structures
- Mock non-deterministic behaviouor 
  - Super globals, time, random, etc. etc. 
  - Mock testing becomes easier
- Exception guarding
  - Fail early, fail often, 
  - Getters should throw if they could not find anything
  - Setters should throw, if they were prevented from applying their operation
- Never extensively use nested if sentences, fail early instead
- Never return false or true, unless method starts with 'is' 
  - isValid, isEnabled, etc. etc.
- Never give methods names like isNotValid, name it isValid and use !isValid
- Never return null, ask questions instead.
  - getUser(): UserDTO|null
  - if (userExist()): bool
- Never add column in SQL without "NOT NULL"
  - SQL makes faster where lookups on NOT NULL columns
- Never use reflection
  - Never use class name reflection
  - Never use method/function name reflection
  - Never use property reflection
  
### TODO
- Rename header to menu
- Rename subPage to Page.
- PHP Codesniffer (Lint)
- Use Monolog for error/exception/fatal handling and logging
- Write tests for Javascript using Puppeteer
- Simple messaging implementation (XMPP, Nodejs SocketCluster) (Mock it aswell)
- Redis implementation (locking, caching) (Mock it)