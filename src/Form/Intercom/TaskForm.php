<?php
declare(strict_types=1);

namespace App\Form\Intercom;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ TextareaType, TextType, SubmitType };
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class TaskForm  extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('phone',
            TextType::class,
            [
                'label' => 'task.phone',
                'attr' => [
                    'placeholder' => 'task.placeholder.phone',
                    'pattern' => '^8\(\d{3}\)\d{3}\-\d{2}\-\d{2}$',
                    'class' => 'form-control',
                ],
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label font-weight-bold',
                ],
            ])
            ->add('fullname',
                TextType::class,
                [
                    'label' => 'task.fullname',
                    'attr' => [
                        'placeholder' => 'task.placeholder.fullname',
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('address',
                TextType::class,
                [
                    'label' => 'task.address',
                    'attr' => [
                        'placeholder' => 'task.placeholder.address',
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('description',
                TextareaType::class,
                [
                    'label' => 'task.description',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'task.placeholder.description',
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                    'constraints' => new Length(['min' => 3]),
                ])
            ->add('status',
                EntityType::class,
                [
                    'label' => 'task.status',
                    'class' => "App\Entity\Intercom\Status",
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('type',
                EntityType::class,
                [
                    'label' => 'task.type',
                    'class' => "App\Entity\Intercom\Type",
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('save',
                SubmitType::class,
                [
                    'label' => 'task.save',
                    'attr' => [
                        'class' => 'btn btn-primary btn-primary-sham m-1',
                    ],
                ])
            ->add('saveandlist',
                SubmitType::class,
                [
                    'label' => 'task.saveandlist',
                    'attr' => [
                        'class' => 'btn btn-primary btn-primary-sham m-1',
                    ],
                ])
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => '\App\Entity\Intercom\Task',
        ]);
    }
}


function fisk(\URFAClient_API $urfa, \PDO $db, $user_id, $summ, $checkID = '') {
    // CREATE TABLE `komtet` (
    //  `id` int(11) NOT NULL AUTO_INCREMENT,
    //  `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    //  `user_id` int(11),
    //  `komtet_id` TEXT,
    //  `external_id` TEXT,
    //  `state` TEXT,
    //  `print_queue_id` TEXT,
    //  `error` TEXT,
    //  PRIMARY KEY (`id`)
    //  ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


    $info = $urfa->rpcf_get_userinfo(array(
        'user_id' => $user_id
    ));

    if (!empty($info['email']) && preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $info['email'])) {
        $clientEmail = $info['email'];
    } else {
        $clientEmail = '';
    }

    // $apipark = false;

    // $groups = $urfa->rpcf_get_groups_for_user(array(
    //              'user_id' => $user_id
    // ));

    // foreach($groups['groups_size'] as $value) {
    //              if ($value['group_id'] === 910) {
    //                              $apipark = true;
    //                              break;
    //              }
    // }

    // if ($apipark === false) {
    //      $shop = array(
    //              'key' => 'U7fvmB',
    //              'secret' => 'C55JThkwyY',
    //              'queue' => '5921'
    //      );
    // } else {
    $shop = array(
        'key' => 'Gd8kXX',
        'secret' => 'K8EPkGrGa5',
        'queue' => '6366'
    );
    // }

    // Убрать!!!
    // file_put_contents('./log/log.log', $user_id . " " . floatval($summ) . " " . $clientEmail . "\n", FILE_APPEND);
    // file_put_contents('./log/log_s.log', $user_id . "\n" . print_r($shop, true) . "\n\n\n", FILE_APPEND);

    $key = $shop['key'];
    $secret = $shop['secret'];
    $logger = null;
    $client = new Client($key, $secret, $logger);
    $manager = new QueueManager($client);
    $manager->registerQueue('Istranet', $shop['queue']);

    $check = Check::createSell($checkID, $clientEmail, TaxSystem::SIMPLIFIED_IN);
    $check->setShouldPrint(true);
    $vat = new Vat(Vat::RATE_NO);
    $position = new Position('Услуги Истранет', floatval($summ), 1, floatval($summ), 0, $vat);
    $check->addPosition($position);
    $payment = new Payment(Payment::TYPE_CARD, floatval($summ));
    $check->addPayment($payment);

    $log = array(
        'id' => '',
        'external_id' => '',
        'state' => '',
        'print_queue_id' => '',
        'error' => ''
    );

    try {
        $output = $manager->putCheck($check, 'Istranet');

        $log['id'] = $output['id'];
        $log['external_id'] = $output['external_id'];
        $log['state'] = $output['state'];
        $log['print_queue_id'] = $output['print_queue_id'];
    } catch (SdkException $e) {
        $log['error'] = $e->getMessage();
    }

    $db->prepare("INSERT INTO komtet (user_id, komtet_id, external_id, state, print_queue_id, error) VALUES (:USER_ID, :KOMTET_ID, :EXTERNAL_ID, :STATE, :PRINT_QUEUE_ID, :ERROR)")->execute(array(
        ":USER_ID" => $user_id,
        ":KOMTET_ID" => $log['id'],
        ":EXTERNAL_ID" => $log['external_id'],
        ":STATE" => $log['state'],
        ":PRINT_QUEUE_ID" => $log['print_queue_id'],
        ":ERROR" => $log['error']
    ));
    return true;
}

