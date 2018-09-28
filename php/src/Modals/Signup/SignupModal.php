<?
declare(strict_types=1);

namespace App\Modals\Signup;

use App\Context;
use App\Modals\Modal;
use App\PHTML;
use Exception;

class SignupModal extends Modal
{

    /**
     * @param Context $ctx
     * @return string
     * @throws Exception
     */
    public function executeModal(Context $ctx): string
    {
        $data = array();
        return PHTML::create("src/Modals/Signup/SignupModal.phtml", $data, $ctx);
    }
}
