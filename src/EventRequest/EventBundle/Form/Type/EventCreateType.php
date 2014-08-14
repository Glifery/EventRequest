<?php

namespace EventRequest\EventBundle\Form\Type;

use EventRequest\EventBundle\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventCreateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                    'label' => 'event.field.name'
                ))
            ->add('description', 'textarea', array(
                    'label' => 'event.field.description'
                ))
            ->add('address', null, array(
                    'label' => 'event.field.address'
                ))
            ->add('country', 'entity', array(
                    'class' => 'EventRequestEventBundle:Country',
                    'property' => 'name',
                    'empty_value' => 'page.filter.empty',
                    'mapped' => false,
                    'label' => 'event.field.country'
                ))
            ->add('date', 'collot_datetime', array(
                    'label' => 'event.field.date',
                    'date_widget' => "single_text",
                    'time_widget' => "single_text",
                    'pickerOptions' => array(
                        'format' => 'dd.mm.yyyy hh:ii',
                        'weekStart' => 0,
                        'startDate' => date('d.m.Y H:i'),
                        'autoclose' => true,
                        'startView' => 'month',
                        'minView' => 'hour',
                        'maxView' => 'decade',
                        'todayBtn' => false,
                        'todayHighlight' => true,
                        'keyboardNavigation' => true,
                        'language' => 'ru',
                        'forceParse' => true,
                        'minuteStep' => 5,
                        'pickerReferer ' => 'default',
                        'pickerPosition' => 'bottom-right',
                        'viewSelect' => 'hour',
                        'showMeridian' => false,
                    )))
            ->add('save', 'submit')
        ;

        $formModifier = function (FormInterface $form, Country $country = null) {
            $cities = ($country === null) ? array() : $country->getCities();

            $form->add('city', 'entity', array(
                    'class'       => 'EventRequestEventBundle:City',
                    'property'    => 'name',
                    'empty_value' => 'page.filter.empty',
                    'choices'     => $cities,
                    'label' => 'event.field.city',
                ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                if ($data = $event->getData() && $city = $event->getData()->getCity()) {
                    $formModifier($event->getForm(), $city->getCountry());
                }else {
                    $formModifier($event->getForm(), null);
                }

            }
        );

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                if ($data = $event->getData() && $city = $event->getData()->getCity()) {
                        $event->getForm()->get('country')->setData($city->getCountry());
                }
            }
        );

        $builder->get('country')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $country = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $country);
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EventRequest\EventBundle\Entity\Event',
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'eventCreateType';
    }
}